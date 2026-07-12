import 'package:dio/dio.dart';
import '../network/api_client.dart';
import '../network/api_exceptions.dart';

class DashboardService {
  final ApiClient _apiClient;
  
  DashboardService(this._apiClient);

  Future<Map<String, dynamic>> getUserStats() async {
    try {
      final response = await _apiClient.get('/orders');
      final orders = response.data['data']['data'] as List;
      
      // Calculate user stats
      int totalOrders = orders.length;
      int paidOrders = 0;
      int pendingOrders = 0;
      int totalPurchasedNotes = 0;
      double totalSpent = 0;
      
      for (var order in orders) {
        if (order['status'] == 'paid') {
          paidOrders++;
          totalSpent += (order['total_amount'] as num).toDouble();
          totalPurchasedNotes += (order['items'] as List).length;
        } else if (order['status'] == 'pending') {
          pendingOrders++;
        }
      }
      
      return {
        'status': 'success',
        'data': {
          'total_orders': totalOrders,
          'paid_orders': paidOrders,
          'pending_orders': pendingOrders,
          'total_purchased_notes': totalPurchasedNotes,
          'total_spent': totalSpent,
        },
      };
    } on ApiException catch (e) {
      rethrow;
    }
  }

  Future<Map<String, dynamic>> getPurchasedNotes() async {
    try {
      final response = await _apiClient.get('/orders');
      final orders = response.data['data']['data'] as List;
      
      List<Map<String, dynamic>> purchasedNotes = [];
      
      for (var order in orders) {
        if (order['status'] == 'paid') {
          for (var item in order['items']) {
            purchasedNotes.add({
              'id': item['note']['id'],
              'title': item['note']['title'],
              'subject': item['note']['subject']?['name'] ?? 'Unknown',
              'price': item['price_at_purchase'],
              'purchased_at': order['created_at'],
              'order_reference': order['reference'],
            });
          }
        }
      }
      
      return {
        'status': 'success',
        'data': purchasedNotes,
      };
    } on ApiException catch (e) {
      rethrow;
    }
  }

  Future<Map<String, dynamic>> getPendingPayments() async {
    try {
      final response = await _apiClient.get('/orders');
      final orders = response.data['data']['data'] as List;
      
      List<Map<String, dynamic>> pendingPayments = [];
      
      for (var order in orders) {
        if (order['status'] == 'pending') {
          List<String> noteTitles = [];
          for (var item in order['items']) {
            noteTitles.add(item['note']['title']);
          }
          
          pendingPayments.add({
            'id': order['id'],
            'order_id': order['reference'],
            'amount': order['total_amount'],
            'status': order['status'],
            'notes': noteTitles,
            'created_at': order['created_at'],
          });
        }
      }
      
      return {
        'status': 'success',
        'data': pendingPayments,
      };
    } on ApiException catch (e) {
      rethrow;
    }
  }
}
