import '../network/api_client.dart';

class PaymentService {
  final ApiClient _apiClient;

  PaymentService(this._apiClient);

  Future<Map<String, dynamic>> createSingleOrder({
    required String materialType,
    required int materialId,
  }) async {
    final response = await _apiClient.post('/orders/single', data: {
      'material_type': materialType,
      'material_id': materialId,
    });
    return response.data as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> initiatePayment({
    required int orderId,
    required String phoneNumber,
  }) async {
    final response = await _apiClient.post('/payments/initiate', data: {
      'order_id': orderId,
      'phone_number': phoneNumber,
    });
    return response.data as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> checkStatus(int orderId) async {
    final response = await _apiClient.get('/orders/$orderId/status');
    return response.data as Map<String, dynamic>;
  }
}
