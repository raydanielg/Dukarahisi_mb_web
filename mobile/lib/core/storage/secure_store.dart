import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../config/constants.dart';

class SecureStore {
  static const _storage = FlutterSecureStorage(
    aOptions: AndroidOptions(
      encryptedSharedPreferences: true,
    ),
  );

  static Future<void> setToken(String token) async {
    await _storage.write(key: Constants.tokenKey, value: token);
  }

  static Future<String?> getToken() async {
    return _storage.read(key: Constants.tokenKey);
  }

  static Future<void> deleteToken() async {
    await _storage.delete(key: Constants.tokenKey);
  }

  static Future<void> clearAll() async {
    await _storage.deleteAll();
  }
}
