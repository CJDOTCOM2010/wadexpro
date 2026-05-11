import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:geolocator/geolocator.dart';
import '../../../../core/network/api_provider.dart';
import '../providers/ride_booking_provider.dart';
import '../providers/live_ride_provider.dart';

class SOSButton extends ConsumerStatefulWidget {
  const SOSButton({super.key});

  @override
  ConsumerState<SOSButton> createState() => _SOSButtonState();
}

class _SOSButtonState extends ConsumerState<SOSButton> with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  bool _isHolding = false;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 2),
    );
    _controller.addStatusListener((status) {
      if (status == AnimationStatus.completed) {
        _triggerSOS();
      }
    });
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  Future<void> _triggerSOS() async {
    final liveRide = ref.read(liveRideProvider);
    final rideRepository = ref.read(rideRepositoryProvider);
    final socketService = ref.read(socketServiceProvider);

    try {
      final position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
      );

      // 1. Primary Trigger: REST API (More reliable for emergencies)
      await rideRepository.triggerSOS(
        lat: position.latitude,
        lng: position.longitude,
        rideId: liveRide.rideId,
      );

      // 2. Secondary Trigger: WebSocket (Real-time update)
      socketService.emit('sos:trigger', {
        'rideId': liveRide.rideId,
        'lat': position.latitude,
        'lng': position.longitude,
      });

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('SOS Alert Sent. Help is on the way.'),
            backgroundColor: Colors.red,
            duration: Duration(seconds: 5),
          ),
        );
        setState(() => _isHolding = false);
        _controller.reset();
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Safety Alert Error: $e'), backgroundColor: Colors.red),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onLongPressStart: (_) {
        setState(() => _isHolding = true);
        _controller.forward();
      },
      onLongPressEnd: (_) {
        setState(() => _isHolding = false);
        _controller.reset();
      },
      child: Stack(
        alignment: Alignment.center,
        children: [
          if (_isHolding)
            SizedBox(
              width: 70,
              height: 70,
              child: CircularProgressIndicator(
                value: _controller.value,
                strokeWidth: 4,
                valueColor: const AlwaysStoppedAnimation<Color>(Colors.red),
              ),
            ),
          Container(
            width: 60,
            height: 60,
            decoration: BoxDecoration(
              color: _isHolding ? Colors.redAccent : Colors.red.withOpacity(0.15),
              shape: BoxShape.circle,
              border: Border.all(color: Colors.redAccent.withOpacity(0.5), width: 2),
              boxShadow: [
                BoxShadow(color: Colors.redAccent.withOpacity(0.3), blurRadius: 15, spreadRadius: 2)
              ],
            ),
            child: Icon(
              Icons.shield,
              color: _isHolding ? Colors.white : Colors.red,
              size: 30,
            ),
          ),
        ],
      ),
    );
  }
}
