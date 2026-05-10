import 'package:google_maps_flutter/google_maps_flutter.dart';
import '../../core/network/api_client.dart';

class LocationRepository {
  final ApiClient _apiClient;
  final String _apiKey = 'YOUR_GOOGLE_MAPS_API_KEY_HERE'; // User will provide

  LocationRepository(this._apiClient);

  /// Fetch place suggestions from Google Places Autocomplete API.
  Future<List<PlaceSuggestion>> searchPlaces(String query, {LatLng? location}) async {
    if (query.isEmpty) return [];

    try {
      final locationBias = location != null 
        ? '&location=${location.latitude},${location.longitude}&radius=50000' 
        : '';
        
      final response = await _apiClient.instance.get(
        'https://maps.googleapis.com/maps/api/place/autocomplete/json',
        queryParameters: {
          'input': query,
          'key': _apiKey,
          'types': 'geocode|establishment',
          if (location != null) 'location': '${location.latitude},${location.longitude}',
          if (location != null) 'radius': '50000',
        },
      );

      final predictions = response.data['predictions'] as List;
      return predictions.map((p) => PlaceSuggestion.fromJson(p)).toList();
    } catch (e) {
      return [];
    }
  }

  /// Fetch precise LatLng for a selected place.
  Future<LatLng?> getPlaceDetails(String placeId) async {
    try {
      final response = await _apiClient.instance.get(
        'https://maps.googleapis.com/maps/api/place/details/json',
        queryParameters: {
          'place_id': placeId,
          'key': _apiKey,
          'fields': 'geometry',
        },
      );

      final location = response.data['result']['geometry']['location'];
      return LatLng(location['lat'], location['lng']);
    } catch (e) {
      return null;
    }
  }
}

class PlaceSuggestion {
  final String placeId;
  final String mainText;
  final String secondaryText;

  PlaceSuggestion({
    required this.placeId,
    required this.mainText,
    required this.secondaryText,
  });

  factory PlaceSuggestion.fromJson(Map<String, dynamic> json) {
    return PlaceSuggestion(
      placeId: json['place_id'],
      mainText: json['structured_formatting']['main_text'],
      secondaryText: json['structured_formatting']['secondary_text'] ?? '',
    );
  }
}
