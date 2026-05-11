import 'dart:async';
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/services/location_service.dart';
import '../../../../core/utils/map_marker_generator.dart';
import '../providers/ride_booking_provider.dart';
import '../providers/ride_booking_state.dart';
import '../providers/live_ride_provider.dart';
import '../providers/live_ride_state.dart';
import '../providers/nearby_drivers_provider.dart';
import '../widgets/ride_selector_sheet.dart';
import '../widgets/searching_overlay.dart';
import '../widgets/sos_button.dart';
import 'search_screen.dart';
import '../widgets/region_selector_overlay.dart';

class RideBookingMapScreen extends ConsumerStatefulWidget {
  const RideBookingMapScreen({super.key});

  @override
  ConsumerState<RideBookingMapScreen> createState() => _RideBookingMapScreenState();
}

class _RideBookingMapScreenState extends ConsumerState<RideBookingMapScreen> {
  final Completer<GoogleMapController> _controller = Completer<GoogleMapController>();
  final LocationService _locationService = LocationService();
  
  BitmapDescriptor? _pickupIcon;
  BitmapDescriptor? _dropoffIcon;
  BitmapDescriptor? _driverIcon;

  static const CameraPosition _initialPosition = CameraPosition(
    target: LatLng(5.6037, -0.1870),
    zoom: 14.4746,
  );

  @override
  void initState() {
    super.initState();
    _setInitialLocation();
    _loadCustomMarkers();
  }

  Future<void> _loadCustomMarkers() async {
    final pickup = await MapMarkerGenerator.createCustomMarker(
      color: Colors.blueAccent,
      icon: Icons.my_location,
    );
    final dropoff = await MapMarkerGenerator.createCustomMarker(
      color: Colors.redAccent,
      icon: Icons.location_on,
    );
    final driver = await MapMarkerGenerator.createDriverMarker(heading: 0);
    
    if (mounted) {
      setState(() {
        _pickupIcon = pickup;
        _dropoffIcon = dropoff;
        _driverIcon = driver;
      });
    }
  }

  Future<void> _setInitialLocation() async {
    try {
      final position = await _locationService.getCurrentLocation();
      if (position != null) {
        final controller = await _controller.future;
        controller.animateCamera(CameraUpdate.newLatLng(
          LatLng(position.latitude, position.longitude),
        ));
        ref.read(rideBookingProvider.notifier).setPickup(
          LatLng(position.latitude, position.longitude),
        );
      }
    } catch (e) {}
  }

  void _onCameraIdle(LatLng center) {
    if (ref.read(liveRideProvider).status == LiveRideStatus.idle) {
      ref.read(nearbyDriversProvider.notifier).startPolling(center);
    }
  }

  @override
  Widget build(BuildContext context) {
    final bookingState = ref.watch(rideBookingProvider);
    final liveRideState = ref.watch(liveRideProvider);
    final isSearching = liveRideState.status == LiveRideStatus.searching;
    final isMatched = liveRideState.status == LiveRideStatus.matched || 
                      liveRideState.status == LiveRideStatus.enRoute ||
                      liveRideState.status == LiveRideStatus.arrived;

    return Scaffold(
      body: Stack(
        children: [
          // The Map Layer
          GoogleMap(
            mapType: MapType.normal,
            initialCameraPosition: _initialPosition,
            myLocationEnabled: true,
            myLocationButtonEnabled: false,
            zoomControlsEnabled: false,
            onMapCreated: (GoogleMapController controller) {
              _controller.complete(controller);
            },
            onCameraIdle: () async {
              final controller = await _controller.future;
              final region = await controller.getVisibleRegion();
              final center = LatLng(
                (region.northeast.latitude + region.southwest.latitude) / 2,
                (region.northeast.longitude + region.southwest.longitude) / 2,
              );
              _onCameraIdle(center);
            },
            onTap: (LatLng location) {
              if (liveRideState.status == LiveRideStatus.idle || 
                  bookingState.status == BookingStatus.ready) {
                ref.read(rideBookingProvider.notifier).setDropoff(location);
              }
            },
            markers: _buildMarkers(bookingState, liveRideState, ref.watch(nearbyDriversProvider)),
          ),

          // Back Button Layer
          Positioned(
            top: 50,
            left: 20,
            child: FloatingActionButton(
              heroTag: 'map_back_button',
              mini: true,
              backgroundColor: Colors.white,
              child: const Icon(Icons.arrow_back, color: Colors.black),
              onPressed: () => Navigator.of(context).pop(),
            ),
          ),

          // Region Selector Layer (Context awareness)
          if (liveRideState.status == LiveRideStatus.idle && bookingState.status == BookingStatus.idle)
            const Positioned(
              top: 110,
              left: 20,
              right: 20,
              child: RegionSelectorOverlay(),
            ),

          // Search Layer (Only shown in discovery)
          if (liveRideState.status == LiveRideStatus.idle)
            Positioned(
              top: (bookingState.status == BookingStatus.idle) ? 180 : 110,
              left: 20,
              right: 20,
              child: Hero(
                tag: 'search_bar',
                child: GestureDetector(
                  onTap: () => Navigator.of(context).push(
                    MaterialPageRoute(builder: (_) => const SearchScreen()),
                  ),
                  child: _buildSearchBox(bookingState),
                ),
              ),
            ),

          // Bottom Selection Sheet (Only shown when ready/estimating)
          if (liveRideState.status == LiveRideStatus.idle && 
              (bookingState.status == BookingStatus.ready || bookingState.status == BookingStatus.estimating))
            const Align(
              alignment: Alignment.bottomCenter,
              child: RideSelectorSheet(),
            ),

          // SOS Button (During active ride)
          if (isMatched)
            const Positioned(
              top: 60,
              right: 20,
              child: SOSButton(),
            ),

          // Live Ride Info (Driver Details)
          if (isMatched)
            _buildDriverInfo(liveRideState),

          // Searching Overlay
          if (isSearching)
            const SearchingOverlay(),
        ],
      ),
      floatingActionButton: (isSearching || isMatched) ? null : Padding(
        padding: EdgeInsets.only(bottom: (bookingState.status == BookingStatus.ready && liveRideState.status == LiveRideStatus.idle) ? 180 : 20),
        child: FloatingActionButton(
          heroTag: 'map_my_location_button',
          onPressed: _setInitialLocation,
          backgroundColor: Colors.white,
          child: const Icon(Icons.my_location, color: AppColors.primary),
        ),
      ),
    );
  }

  Widget _buildSearchBox(RideBookingState bookingState) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: const [BoxShadow(color: Colors.black12, blurRadius: 10)],
      ),
      child: Material(
        color: Colors.transparent,
        child: Row(
          children: [
            const Icon(Icons.menu, color: AppColors.primary),
            const SizedBox(width: 16),
            Text(
              bookingState.dropoff != null ? 'Selected Destination' : 'Enter Destination...', 
              style: TextStyle(
                color: bookingState.dropoff != null ? AppColors.primary : Colors.grey,
                fontWeight: bookingState.dropoff != null ? FontWeight.bold : FontWeight.normal,
              )
            ),
            const Spacer(),
            CircleAvatar(
              backgroundColor: AppColors.accent,
              radius: 15,
              child: const Icon(Icons.person, size: 20, color: AppColors.primary),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildDriverInfo(LiveRideState state) {
    return Align(
      alignment: Alignment.bottomCenter,
      child: Container(
        padding: const EdgeInsets.all(24),
        decoration: const BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
          boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10)],
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Row(
              children: [
                CircleAvatar(
                  radius: 30,
                  backgroundColor: AppColors.primary.withOpacity(0.1),
                  child: const Icon(Icons.person, color: AppColors.primary, size: 30),
                ),
                const SizedBox(width: 16),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(state.driverData?['driverName'] ?? 'Driver', style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                      Text(state.driverData?['vehicleModel'] ?? 'Toyota Corolla', style: const TextStyle(color: AppColors.textMuted)),
                    ],
                  ),
                ),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(color: AppColors.accent, borderRadius: BorderRadius.circular(8)),
                      child: Text(state.driverData?['vehiclePlate'] ?? 'ABC-123', style: const TextStyle(fontWeight: FontWeight.bold)),
                    ),
                    const SizedBox(height: 4),
                    const Row(children: [Icon(Icons.star, color: Colors.orange, size: 16), Text('4.8')]),
                  ],
                ),
              ],
            ),
            const SizedBox(height: 24),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: [
                _buildActionIcon(Icons.call, 'Call'),
                GestureDetector(
                  onTap: () {
                    if (state.rideId != null) {
                      context.push('/chat', extra: {
                        'rideId': state.rideId,
                        'name': state.driverData?['name'] ?? 'Driver',
                      });
                    }
                  },
                  child: _buildActionIcon(Icons.message, 'Chat'),
                ),
                _buildActionIcon(Icons.close, 'Cancel', color: AppColors.error),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildActionIcon(IconData icon, String label, {Color color = AppColors.primary}) {
    return Column(
      children: [
        Container(
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(color: color.withOpacity(0.1), shape: BoxShape.circle),
          child: Icon(icon, color: color),
        ),
        const SizedBox(height: 4),
        Text(label, style: TextStyle(fontSize: 12, color: color)),
      ],
    );
  }

  Set<Marker> _buildMarkers(bookingState, liveRideState, List<NearbyDriver> nearbyDrivers) {
    final markers = <Marker>{};

    // Phantom cars (Nearby Drivers) - Only show when idle
    if (liveRideState.status == LiveRideStatus.idle) {
      for (var driver in nearbyDrivers) {
        markers.add(Marker(
          markerId: MarkerId('nearby_${driver.id}'),
          position: driver.location,
          icon: _driverIcon ?? BitmapDescriptor.defaultMarkerWithHue(BitmapDescriptor.hueYellow),
          rotation: driver.heading ?? 0,
          flat: true,
          anchor: const Offset(0.5, 0.5),
        ));
      }
    }
    
    if (bookingState.pickup != null) {
      markers.add(Marker(
        markerId: const MarkerId('pickup'),
        position: bookingState.pickup!,
        icon: _pickupIcon ?? BitmapDescriptor.defaultMarkerWithHue(BitmapDescriptor.hueAzure),
      ));
    }
    if (bookingState.dropoff != null) {
      markers.add(Marker(
        markerId: const MarkerId('dropoff'),
        position: bookingState.dropoff!,
        icon: _dropoffIcon ?? BitmapDescriptor.defaultMarkerWithHue(BitmapDescriptor.hueOrange),
      ));
    }
    if (liveRideState.driverLocation != null) {
      markers.add(Marker(
        markerId: const MarkerId('driver'),
        position: liveRideState.driverLocation!,
        rotation: liveRideState.driverHeading ?? 0,
        flat: true,
        anchor: const Offset(0.5, 0.5),
        icon: _driverIcon ?? BitmapDescriptor.defaultMarkerWithHue(BitmapDescriptor.hueYellow), 
      ));
    }
    return markers;
  }
}
