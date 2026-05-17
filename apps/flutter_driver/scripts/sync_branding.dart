import 'dart:convert';
import 'dart:io';

Future<void> main() async {
  print('--- Starting WADEXPRO Driver Branding Sync ---');

  // 1. Read API URL from .env
  final envFile = File('.env');
  if (!envFile.existsSync()) {
    print('Error: .env file not found.');
    return;
  }
  final envLines = await envFile.readAsLines();
  String? baseUrl;
  for (var line in envLines) {
    if (line.startsWith('BASE_URL=')) {
      baseUrl = line.split('=')[1].trim();
      break;
    }
  }

  if (baseUrl == null || baseUrl.isEmpty) {
    print('Error: BASE_URL not found in .env');
    return;
  }

  final apiUrl = '$baseUrl/platform/splash/driver'; // or the respective endpoint
  print('Fetching branding config from: $apiUrl');

  // 2. Fetch config from API
  final httpClient = HttpClient();
  try {
    final request = await httpClient.getUrl(Uri.parse(apiUrl));
    final response = await request.close();
    if (response.statusCode != 200) {
      print('Error: API returned status ${response.statusCode}');
      return;
    }

    final responseBody = await response.transform(utf8.decoder).join();
    final data = jsonDecode(responseBody);
    final configData = data['data'];

    if (configData == null) {
      print('Error: Invalid API response format (missing "data" key)');
      return;
    }

    final appName = configData['appName'];
    final appIconPath = configData['appIconUrl']; // Assuming backend returns relative path or full url

    print('App Name received: $appName');
    print('App Icon URL received: $appIconPath');

    // 3. Rename the app
    if (appName != null && appName.toString().isNotEmpty) {
      print('Updating App Name to: $appName...');
      final renameResult = await Process.run('flutter', ['pub', 'run', 'rename', 'setAppName', '--targets', 'ios,android', '--value', appName]);
      if (renameResult.exitCode == 0) {
        print('Successfully updated App Name.');
      } else {
        print('Failed to update App Name: ${renameResult.stderr}');
      }
    }

    // 4. Download icon and configure flutter_launcher_icons
    if (appIconPath != null && appIconPath.toString().isNotEmpty) {
      final fullIconUrl = appIconPath.startsWith('http') ? appIconPath : '$baseUrl/storage/$appIconPath';
      print('Downloading App Icon from: $fullIconUrl');
      
      final iconRequest = await httpClient.getUrl(Uri.parse(fullIconUrl));
      final iconResponse = await iconRequest.close();
      if (iconResponse.statusCode == 200) {
        final iconDirectory = Directory('assets/branding');
        if (!iconDirectory.existsSync()) {
          iconDirectory.createSync(recursive: true);
        }
        final iconFile = File('assets/branding/app_icon.png');
        await iconResponse.pipe(iconFile.openWrite());
        print('App Icon downloaded successfully.');

        // Create/Update flutter_launcher_icons.yaml
        final yamlContent = '''
flutter_launcher_icons:
  android: "launcher_icon"
  ios: true
  image_path: "assets/branding/app_icon.png"
  min_sdk_android: 21
''';
        await File('flutter_launcher_icons.yaml').writeAsString(yamlContent);
        
        print('Generating launcher icons...');
        final iconResult = await Process.run('flutter', ['pub', 'run', 'flutter_launcher_icons']);
        if (iconResult.exitCode == 0) {
          print('Successfully generated launcher icons.');
        } else {
          print('Failed to generate launcher icons: ${iconResult.stderr}');
        }
      } else {
        print('Failed to download icon (status ${iconResponse.statusCode})');
      }
    }

    print('--- Branding Sync Complete! ---');
  } catch (e) {
    print('Error during sync: $e');
  } finally {
    httpClient.close();
  }
}
