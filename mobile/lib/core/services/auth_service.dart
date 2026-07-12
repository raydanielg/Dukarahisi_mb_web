import 'package:dio/dio.dart';
import '../network/api_client.dart';
import '../network/api_exceptions.dart';
import '../storage/local_cache.dart';
import '../config/constants.dart';

class AuthService {
  final ApiClient _apiClient;
  
  AuthService(this._apiClient);

  Future<Map<String, dynamic>> register({
    required String name,
    required String phoneNumber,
    required String email,
    required String password,
  }) async {
    try {
      final response = await _apiClient.post(
        '/api/register',
        data: {
          'name': name,
          'phone_number': phoneNumber,
          'email': email,
          'password': password,
        },
      );
      
      return response.data;
    } on ApiException catch (e) {
      rethrow;
    }
  }

  Future<Map<String, dynamic>> verifyOtp({
    required String phoneNumber,
    required String otpCode,
  }) async {
    try {
      final response = await _apiClient.post(
        '/api/verify-otp',
        data: {
          'phone_number': phoneNumber,
          'otp_code': otpCode,
        },
      );
      
      // Save token if verification successful
      if (response.data['status'] == 'success') {
        final token = response.data['data']['token'];
        await LocalCache.set(Constants.tokenKey, token);
        await LocalCache.set(Constants.isLoggedInKey, true);
      }
      
      return response.data;
    } on ApiException catch (e) {
      rethrow;
    }
  }

  Future<Map<String, dynamic>> login({
    required String phoneNumber,
    required String password,
  }) async {
    try {
      final response = await _apiClient.post(
        '/api/login',
        data: {
          'phone_number': phoneNumber,
          'password': password,
        },
      );
      
      // Save token if login successful
      if (response.data['status'] == 'success') {
        final token = response.data['data']['token'];
        await LocalCache.set(Constants.tokenKey, token);
        await LocalCache.set(Constants.isLoggedInKey, true);
      }
      
      return response.data;
    } on ApiException catch (e) {
      rethrow;
    }
  }

  Future<Map<String, dynamic>> forgotPassword({
    required String phoneNumber,
  }) async {
    try {
      final response = await _apiClient.post(
        '/api/forgot-password',
        data: {
          'phone_number': phoneNumber,
        },
      );
      
      return response.data;
    } on ApiException catch (e) {
      rethrow;
    }
  }

  Future<Map<String, dynamic>> resetPassword({
    required String phoneNumber,
    required String otpCode,
    required String password,
  }) async {
    try {
      final response = await _apiClient.post(
        '/api/reset-password',
        data: {
          'phone_number': phoneNumber,
          'otp_code': otpCode,
          'password': password,
          'password_confirmation': password,
        },
      );
      
      return response.data;
    } on ApiException catch (e) {
      rethrow;
    }
  }

  Future<Map<String, dynamic>> me() async {
    try {
      final response = await _apiClient.get('/api/me');
      return response.data;
    } on ApiException catch (e) {
      rethrow;
    }
  }

  Future<Map<String, dynamic>> logout() async {
    try {
      final response = await _apiClient.post('/api/logout');
      
      // Clear local storage
      await LocalCache.remove(Constants.tokenKey);
      await LocalCache.remove(Constants.isLoggedInKey);
      
      return response.data;
    } on ApiException catch (e) {
      rethrow;
    }
  }

  Future<bool> isLoggedIn() async {
    return LocalCache.get<bool>(Constants.isLoggedInKey) ?? false;
  }

  Future<String?> getToken() async {
    return LocalCache.get<String>(Constants.tokenKey);
  }
}
