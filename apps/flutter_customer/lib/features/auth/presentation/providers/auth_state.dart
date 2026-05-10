enum AuthStatus {
  initial,
  loading,
  codeSent,
  authenticated,
  unauthenticated,
  error,
}

class AuthState {
  final AuthStatus status;
  final String? identifier;
  final String? errorMessage;
  final dynamic user; // We can type this once User model is ready

  AuthState({
    this.status = AuthStatus.initial,
    this.identifier,
    this.errorMessage,
    this.user,
  });

  AuthState copyWith({
    AuthStatus? status,
    String? identifier,
    String? errorMessage,
    dynamic user,
  }) {
    return AuthState(
      status: status ?? this.status,
      identifier: identifier ?? this.identifier,
      errorMessage: errorMessage ?? this.errorMessage,
      user: user ?? this.user,
    );
  }
}
