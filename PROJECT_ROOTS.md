# WADEXPRO Project Structure

This document serves as a reference for the core structure of the **WADEXPRO** project to ensure consistency and avoid confusion with other projects (like `diabetic-app-healthbud`).

## Project Root
- **Path**: `c:\Users\SCANNINWORLD TECHS\Desktop\Project Files\wadexpro`
- **Core Framework**: Laravel (Backend, API, Socket Server)

## Sub-Applications (`/apps`)
The project follows a monorepo-style structure with several distinct components:

1. **Admin Panel** (`apps/admin_panel`)
   - Purpose: Centralized management and orchestration portal.

2. **Customer App** (`apps/flutter_customer`)
   - Purpose: Flutter-based mobile application for end-users/customers.

3. **Driver App** (`apps/flutter_driver`)
   - Purpose: Flutter-based mobile application for delivery drivers.

4. **Landing Site** (`apps/landing_site`)
   - Purpose: Public-facing marketing and informational website.

## Environment & Infrastructure
- **GitHub Repository**: [https://github.com/CJDOTCOM2010/wadexpro](https://github.com/CJDOTCOM2010/wadexpro)
- **Primary Branch**: `main`
- **Socket Server**: Located in `socket-server/` for real-time communication.
