import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../constants/endpoints.dart';

class ApiClient {
  final Dio dio = Dio(BaseOptions(
    baseUrl: Endpoints.baseUrl,
    connectTimeout: const Duration(seconds: 10),
    receiveTimeout: const Duration(seconds: 10),
  ));

  final _secureStorage = const FlutterSecureStorage();

  ApiClient() {
    dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        // Retrieve credentials stored securely or use default configuration values
        final apiKey = await _secureStorage.read(key: 'api_key') ?? 'nvx_pk_live_B13S1gJQ73anYvlS3JjAP2CVeCv1xjY5';
        final apiSecret = await _secureStorage.read(key: 'api_secret') ?? 'nvx_sk_live_c5hdF5i8HSzQhY1KMjn7nqhpTkx8u9eUXNx2mO8TZhU4dJ6R';
        final merchantId = await _secureStorage.read(key: 'merchant_id') ?? '019f9dc9-a3aa-7076-b865-1f4ca42e790c';

        options.headers['x-api-key'] = apiKey;
        options.headers['x-api-secret'] = apiSecret;
        options.headers['x-merchant-id'] = merchantId;
        options.headers['Accept'] = 'application/json';

        return handler.next(options);
      },
      onError: (DioException e, handler) {
        // Handle global HTTP exceptions
        return handler.next(e);
      },
    ));
  }
}
