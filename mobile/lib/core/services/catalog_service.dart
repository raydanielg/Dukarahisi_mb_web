import '../network/api_client.dart';

class CatalogService {
  final ApiClient _apiClient;

  CatalogService(this._apiClient);

  Future<Map<String, dynamic>> getLevels() async {
    try {
      final response = await _apiClient.get('/catalog/levels');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  Future<Map<String, dynamic>> getClasses(int levelId) async {
    try {
      final response = await _apiClient.get('/catalog/classes/$levelId');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  Future<Map<String, dynamic>> getSubjects(int classId) async {
    try {
      final response = await _apiClient.get('/catalog/subjects/$classId');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  Future<Map<String, dynamic>> getTopics(int subjectId) async {
    try {
      final response = await _apiClient.get('/catalog/topics/$subjectId');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  Future<Map<String, dynamic>> getMaterials(int topicId) async {
    try {
      final response = await _apiClient.get('/catalog/materials/$topicId');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }
}
