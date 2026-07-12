import '../network/api_client.dart';

class ActivityService {
  final ApiClient _apiClient;

  ActivityService(this._apiClient);

  Future<Map<String, dynamic>> getActivities() async {
    try {
      final response = await _apiClient.get('/activities');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }
}
