import 'dart:convert';
import 'dart:io';

Future<void> main() async {
  print('--- Starting WADEXPRO Branding Sync ---');

  print('Running local Laravel artisan command to fetch branding config...');
  
  try {
    final rootDir = Directory.current.parent.parent.path;
    final artisanResult = await Process.run('php', ['artisan', 'platform:build-branding', 'customer'], workingDirectory: rootDir);
    
    if (artisanResult.exitCode != 0) {
      print('Error running artisan command: ${artisanResult.stderr}');
      return;
    }

    final responseBody = artisanResult.stdout.toString().trim();
    final configData = jsonDecode(responseBody);

    if (configData == null) {
      print('Error: Invalid API response format');
      return;
    }

    final appName = configData['appName'];
    final appIconPath = configData['appIconUrl'];

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
      // The icon path will be relative from artisan, e.g., /storage/branding/icons/xyz.png
      // So we will copy it from the public/storage directory instead of downloading it over HTTP!
      // This is much more reliable since we are building locally.
      
      final publicStoragePath = appIconPath.toString().replaceFirst('/storage/', '');
      final localIconPath = '${Directory.current.parent.parent.path}/public/storage/$publicStoragePath';
      
      print('Copying App Icon from local storage: $localIconPath');
      
      final localIconFile = File(localIconPath);
      
      if (localIconFile.existsSync()) {
        final iconDirectory = Directory('assets/branding');
        if (!iconDirectory.existsSync()) {
          iconDirectory.createSync(recursive: true);
        }
        final iconFile = File('assets/branding/app_icon.png');
        await localIconFile.copy(iconFile.path);
        print('App Icon copied successfully.');

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
        print('Failed to find icon at $localIconPath');
      }
    }

    print('--- Branding Sync Complete! ---');
  } catch (e) {
    print('Error during sync: $e');
  }
}
