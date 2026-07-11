class ApiException implements Exception {
  final String message;
  final int? statusCode;

  ApiException(this.message, {this.statusCode});

  @override
  String toString() => message;
}

class NetworkException extends ApiException {
  NetworkException([String message = 'No internet connection.']) : super(message);
}

class UnauthorizedException extends ApiException {
  UnauthorizedException([String message = 'Session expired. Please login again.']) : super(message, statusCode: 401);
}

class ValidationException extends ApiException {
  final Map<String, dynamic> errors;
  ValidationException(this.errors, [String message = 'Validation failed.']) : super(message, statusCode: 422);
}
