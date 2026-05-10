import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:live_activities/live_activities.dart';

final liveActivityServiceProvider = Provider<LiveActivityService>((ref) {
  return LiveActivityService();
});

class LiveActivityService {
  final _liveActivitiesPlugin = LiveActivities();
  String? _latestActivityId;

  Future<void> init() async {
    await _liveActivitiesPlugin.init(appGroupId: 'group.com.wadexpro.customer');
  }

  Future<void> startRideActivity({
    required String status,
    required String driverName,
    required String vehicle,
    required String eta,
  }) async {
    final activityModel = {
      'status': status,
      'driverName': driverName,
      'vehicle': vehicle,
      'eta': eta,
    };

    _latestActivityId = await _liveActivitiesPlugin.createActivity(
      activityModel,
    );
  }

  Future<void> updateRideActivity({
    required String status,
    required String eta,
  }) async {
    if (_latestActivityId == null) return;

    final updatedModel = {
      'status': status,
      'eta': eta,
    };

    await _liveActivitiesPlugin.updateActivity(_latestActivityId!, updatedModel);
  }

  Future<void> endRideActivity() async {
    if (_latestActivityId != null) {
      await _liveActivitiesPlugin.endActivity(_latestActivityId!);
      _latestActivityId = null;
    } else {
      await _liveActivitiesPlugin.endAllActivities();
    }
  }
}
