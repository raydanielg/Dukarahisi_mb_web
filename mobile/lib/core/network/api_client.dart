import 'package:dio/dio.dart';
import '../config/app_config.dart';
import '../config/constants.dart';
import 'api_exceptions.dart';
import 'auth_interceptor.dart';

class ApiClient {
  late final Dio _dio;

  ApiClient() {
    _dio = Dio(
      BaseOptions(
        baseUrl: AppConfig.baseUrl,
        connectTimeout: const Duration(milliseconds: Constants.connectionTimeout),
        receiveTimeout: const Duration(milliseconds: Constants.receiveTimeout),
        headers: {'Accept': 'application/json'},
      ),
    );
    _dio.interceptors.add(AuthInterceptor());
    if (AppConfig.enableLogging) {
      _dio.interceptors.add(LogInterceptor(requestBody: true, responseBody: true));
    }
  }

  Dio get dio => _dio;

  Future<Response> get(String path, {Map<String, dynamic>? queryParameters}) async {
    try {
      return await _dio.get(path, queryParameters: queryParameters);
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  Future<Response> post(String path, {dynamic data}) async {
    try {
      return await _dio.post(path, data: data);
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  Future<Response> put(String path, {dynamic data}) async {
    try {
      return await _dio.put(path, data: data);
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  Future<Response> delete(String path) async {
    try {
      return await _dio.delete(path);
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  ApiException _handleError(DioException e) {
    switch (e.type) {
      case DioExceptionType.connectionTimeout:
      case DioExceptionType.receiveTimeout:
      case DioExceptionType.sendTimeout:
        return NetworkException('Connection timeout. Please try again.');
      case DioExceptionType.connectionError:
      case DioExceptionType.unknown:
        return NetworkException('No internet connection.');
      case DioExceptionType.badResponse:
        final statusCode = e.response?.statusCode;
        final data = e.response?.data;
        if (statusCode == 401) return UnauthorizedException();
        if (statusCode == 422 && data is Map<String, dynamic>) {
          return ValidationException(data['errors'] ?? {}, data['message'] ?? 'Validation failed.');
        }
        return ApiException(data?['message'] ?? 'Something went wrong.', statusCode: statusCode);
      default:
        return ApiException(e.message ?? 'An error occurred.');
    }
  }
}
