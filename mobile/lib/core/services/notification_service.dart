import '../network/api_client.dart';

class NotificationService {
  final ApiClient _apiClient;

  NotificationService(this._apiClient);

  Future<Map<String, dynamic>> getNotifications() async {
    try {
      final response = await _apiClient.get('/notifications');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  Future<Map<String, dynamic>> markAsRead(int id) async {
    try {
      final response = await _apiClient.post('/notifications/$id/mark-read');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  Future<Map<String, dynamic>> markAllAsRead() async {
    try {
      final response = await _apiClient.post('/notifications/mark-all-read');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }
}
