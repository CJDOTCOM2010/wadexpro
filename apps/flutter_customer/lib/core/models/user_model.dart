class UserModel {
  final String id;        // UUID primary key from DB
  final String? uuid;     // alias (same as id for this schema)
  final String name;
  final String? email;
  final String? phone;
  final String? avatarUrl;
  final String userType;
  final String? referralCode;
  final double walletBalance;
  final Map<String, dynamic>? driverProfile;

  UserModel({
    required this.id,
    this.uuid,
    required this.name,
    this.email,
    this.phone,
    this.avatarUrl,
    required this.userType,
    this.referralCode,
    required this.walletBalance,
    this.driverProfile,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id']?.toString() ?? '',
      uuid: json['uuid']?.toString() ?? json['id']?.toString(),
      name: json['name'] ?? 'Wadex User',
      email: json['email'],
      phone: json['phone'],
      avatarUrl: json['avatar_url'],
      userType: json['user_type'] ?? 'customer',
      referralCode: json['referral_code'],
      walletBalance: (json['wallet_balance'] as num?)?.toDouble() ?? 0.0,
      driverProfile: json['driver_profile'] as Map<String, dynamic>?,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'uuid': uuid,
      'name': name,
      'email': email,
      'phone': phone,
      'avatar_url': avatarUrl,
      'user_type': userType,
      'referral_code': referralCode,
      'wallet_balance': walletBalance,
      'driver_profile': driverProfile,
    };
  }
}
