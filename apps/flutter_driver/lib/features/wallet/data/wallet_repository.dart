import '../../../core/network/api_client.dart';

class WalletRepository {
  final ApiClient _apiClient;

  WalletRepository(this._apiClient);

  /// Fetch current driver balance.
  Future<Map<String, dynamic>> getBalance() async {
    final response = await _apiClient.instance.get('/v1/logistics/wallet/balance');
    return response.data;
  }

  /// Fetch paginated transaction ledger.
  Future<List<dynamic>> getTransactions() async {
    final response = await _apiClient.instance.get('/v1/logistics/wallet/transactions');
    // Laravel pagination returns data inside 'data' field
    return response.data['data'] ?? []; 
  }

  /// Fetch daily earnings for the last 7 days (Chart data).
  Future<Map<String, dynamic>> getWeeklySummary() async {
    try {
      final response = await _apiClient.instance.get('/v1/logistics/wallet/weekly-summary');
      return response.data['data'];
    } catch (e) {
      return {'days': [], 'total_weekly': 0.0};
    }
  }

  /// Submit a withdrawal/payout request.
  Future<Map<String, dynamic>> requestPayout(double amount) async {
    final response = await _apiClient.instance.post('/v1/logistics/wallet/payout', data: {
      'amount': amount,
    });
    return response.data['data'];
  }
}
