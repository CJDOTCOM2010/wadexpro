// ignore: avoid_web_libraries_in_flutter
import 'dart:html' as html;

String? getLocalStorage(String key) => html.window.localStorage[key];
void setLocalStorage(String key, String value) => html.window.localStorage[key] = value;
void removeLocalStorage(String key) => html.window.localStorage.remove(key);
void clearLocalStorage() => html.window.localStorage.clear();
