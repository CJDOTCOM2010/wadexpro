import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/utils/map_marker_generator.dart';
import '../providers/driver_status_provider.dart';
import '../providers/active_trip_provider.dart';
import '../widgets/incoming_ride_overlay.dart';
import '../widgets/active_trip_panel.dart';
import '../widgets/sos_button.dart';
import '../../../wallet/presentation/pages/wallet_screen.dart';
import '../../../profile/presentation/pages/profile_screen.dart';
import '../../../../core/widgets/dynamic_glass_card.dart';

class DriverHomeScreen extends ConsumerStatefulWidget {
  const DriverHomeScreen({super.key});

  @override
  ConsumerState<DriverHomeScreen> createState() => _DriverHomeScreenState();
}

class _DriverHomeScreenState extends ConsumerState<DriverHomeScreen> {
  final Completer<GoogleMapController> _controller = Completer<GoogleMapController>();
  
  BitmapDescriptor? _pickupIcon;
  BitmapDescriptor? _dropoffIcon;
  BitmapDescriptor? _driverIcon;

  static const CameraPosition _initialPosition = CameraPosition(
    target: LatLng(9.0820, 8.6753),
    zoom: 14.4746,
  );

  @override
  void initState() {
    super.initState();
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

  @override
  Widget build(BuildContext context) {
    final statusState = ref.watch(driverStatusProvider);
    final tripState = ref.watch(activeTripProvider);
    final isTripActive = tripState.status != ActiveTripStatus.none;

    // Auto-center map when position updates
    if (statusState.currentPosition != null) {
      _controller.future.then((controller) {
        controller.animateCamera(CameraUpdate.newLatLng(
          LatLng(statusState.currentPosition!.latitude, statusState.currentPosition!.longitude),
        ));
      });
    }

    return Scaffold(
      body: Stack(
        children: [
          // Map Background
          GoogleMap(
            mapType: MapType.normal,
            initialCameraPosition: _initialPosition,
            myLocationEnabled: true,
            myLocationButtonEnabled: false,
            zoomControlsEnabled: false,
            onMapCreated: (GoogleMapController controller) {
              _controller.complete(controller);
            },
            markers: _buildMarkers(tripState, statusState),
          ),

          // Top Command Bar (Only shown when no trip active)
          if (!isTripActive)
            Positioned(
              top: 60,
              left: 20,
              right: 20,
              child: DynamicGlassCard(
                padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
                child: Row(
                  children: [
                    GestureDetector(
                      onTap: () {
                        HapticFeedback.lightImpact();
                        // Profile Navigation
                        Navigator.push(context, MaterialPageRoute(builder: (_) => ProfileScreen()));
                      },
                      child: Hero(
                        tag: 'driver_avatar',
                        child: CircleAvatar(
                          backgroundColor: AppColors.primaryNavy.withOpacity(0.1),
                          child: const Icon(Icons.person, color: AppColors.primaryNavy),
                        ),
                      ),
                    ),
                    const SizedBox(width: 16),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text('STATUS', style: TextStyle(color: AppColors.textMuted, fontSize: 10, letterSpacing: 1, fontWeight: FontWeight.bold)),
                        const SizedBox(height: 2),
                        Text(
                          statusState.isOnline ? 'ONLINE' : 'OFFLINE',
                          style: TextStyle(
                            color: statusState.isOnline ? Colors.green.shade700 : AppColors.obsidianDark,
                            fontWeight: FontWeight.w900,
                            fontSize: 18,
                          ),
                        ),
                      ],
                    ),
                    const Spacer(),
                    Switch(
                      value: statusState.isOnline,
                      activeThumbColor: Colors.green.shade600,
                      activeTrackColor: Colors.green.shade200,
                      inactiveThumbColor: AppColors.textMuted,
                      inactiveTrackColor: Colors.grey.shade300,
                      onChanged: (val) {
                        HapticFeedback.mediumImpact();
                        ref.read(driverStatusProvider.notifier).toggleStatus();
                      },
                    ),
                  ],
                ),
              ),
            ),

          // SOS Button (During active ride)
          if (isTripActive)
            const Positioned(
              top: 60,
              right: 20,
              child: SOSButton(),
            ),

          // Discovery Bottom Panel (Only shown when no trip active)
          if (!isTripActive)
            Align(
              alignment: Alignment.bottomCenter,
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: DynamicGlassCard(
                  padding: const EdgeInsets.all(24),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceAround,
                        children: [
                          _buildStatColumn(context, 'EARNINGS', 'GH₵0.00', onTap: () {
                            HapticFeedback.selectionClick();
                            // Wallet Navigation
                            Navigator.push(context, MaterialPageRoute(builder: (_) => WalletScreen()));
                          }),
                          const VerticalDivider(width: 40, color: Colors.black12),
                          _buildStatColumn(context, 'RATING', '5.0 ★'),
                          const VerticalDivider(width: 40, color: Colors.black12),
                          _buildStatColumn(context, 'TRIPS', '0'),
                        ],
                      ),
                      if (!statusState.isOnline) ...[
                        const SizedBox(height: 24),
                        const Text(
                          'You are currently offline. Toggle the switch to start receiving ride requests.',
                          textAlign: TextAlign.center,
                          style: TextStyle(color: AppColors.obsidianDark, fontWeight: FontWeight.w500),
                        ),
                      ] else ...[
                         const SizedBox(height: 24),
                         const LinearProgressIndicator(color: AppColors.primaryNavy, backgroundColor: Colors.white54),
                         const SizedBox(height: 12),
                         const Text('Searching for requests...', style: TextStyle(color: AppColors.primaryNavy, fontWeight: FontWeight.bold)),
                      ]
                    ],
                  ),
                ),
              ),
            ),

          // Active Trip Panel
          if (isTripActive)
            const Align(
              alignment: Alignment.bottomCenter,
              child: ActiveTripPanel(),
            ),

          // Incoming Request Overlay Layer
          const IncomingRideOverlay(),
        ],
      ),
    );
  }

  Widget _buildStatColumn(BuildContext context, String label, String value, {VoidCallback? onTap}) {
    return GestureDetector(
      onTap: onTap,
      behavior: HitTestBehavior.opaque,
      child: Column(
        children: [
          Text(label, style: const TextStyle(color: AppColors.textMuted, fontSize: 10, fontWeight: FontWeight.bold)),
          const SizedBox(height: 4),
          Text(value, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppColors.primaryNavy)),
        ],
      ),
    );
  }

  Set<Marker> _buildMarkers(ActiveTripState tripState, DriverStatusState statusState) {
    final markers = <Marker>{};

    // Self Marker (The Car)
    if (statusState.currentPosition != null) {
      markers.add(Marker(
        markerId: const MarkerId('self'),
        position: LatLng(statusState.currentPosition!.latitude, statusState.currentPosition!.longitude),
        rotation: statusState.currentPosition!.heading,
        flat: true,
        anchor: const Offset(0.5, 0.5),
        icon: _driverIcon ?? BitmapDescriptor.defaultMarkerWithHue(BitmapDescriptor.hueYellow),
      ));
    }

    if (tripState.status == ActiveTripStatus.none) return markers;
    
    final ride = tripState.rideData!;

    markers.add(Marker(
      markerId: const MarkerId('pickup'),
      position: LatLng(ride['pickupLat'] as double, ride['pickupLng'] as double),
      icon: _pickupIcon ?? BitmapDescriptor.defaultMarkerWithHue(BitmapDescriptor.hueAzure),
      infoWindow: const InfoWindow(title: 'Pickup Location'),
    ));

    markers.add(Marker(
      markerId: const MarkerId('dropoff'),
      position: LatLng(ride['dropoffLat'] as double, ride['dropoffLng'] as double),
      icon: _dropoffIcon ?? BitmapDescriptor.defaultMarkerWithHue(BitmapDescriptor.hueRed),
      infoWindow: const InfoWindow(title: 'Destination'),
    ));

    return markers;
  }
}
