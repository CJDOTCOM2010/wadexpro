import 'dart:async';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/services/location_repository.dart';
import '../../../../core/network/api_provider.dart';

final locationRepositoryProvider = Provider((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return LocationRepository(apiClient);
});

final searchProvider = StateNotifierProvider<SearchNotifier, List<PlaceSuggestion>>((ref) {
  final repository = ref.watch(locationRepositoryProvider);
  return SearchNotifier(repository);
});

class SearchNotifier extends StateNotifier<List<PlaceSuggestion>> {
  final LocationRepository _repository;
  Timer? _debounce;

  SearchNotifier(this._repository) : super([]);

  void onQueryChanged(String query) {
    if (_debounce?.isActive ?? false) _debounce!.cancel();
    
    _debounce = Timer(const Duration(milliseconds: 300), () async {
      if (query.isEmpty) {
        state = [];
        return;
      }
      
      final suggestions = await _repository.searchPlaces(query);
      state = suggestions;
    });
  }

  @override
  void dispose() {
    _debounce?.cancel();
    super.dispose();
  }
}
