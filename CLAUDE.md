# CLAUDE.md — ENTERPRISE DEVELOPMENT RULES & SYSTEM STANDARDS

---

# 1. SYSTEM OVERVIEW

This project is a **production-grade, enterprise-level ride-hailing, courier, and mobility platform**.

This is NOT a prototype.

All implementations MUST be:

* Production-ready
* Fully functional
* Scalable
* Secure
* Maintainable

The system MUST support real-world deployment and high user traffic.

---

# 2. ABSOLUTE DEVELOPMENT PRINCIPLES

The following rules are NON-NEGOTIABLE:

* DO NOT generate:

  * Fake code
  * Placeholder logic
  * Mock systems (except isolated test environments)
  * Incomplete features

* Every feature MUST:

  * Be fully implemented end-to-end
  * Be connected to backend, frontend, and database
  * Follow real-world logic and constraints

* ALWAYS:

  * Validate inputs
  * Secure outputs
  * Log critical actions
  * Handle failures gracefully

---

# 3. SYSTEM ARCHITECTURE (STRICT)

The platform MUST follow a hybrid architecture:

### Core Stack:

* Backend Core: Laravel (PHP 8.4.x, Laravel 12)
* Local Environment: Laravel Herd + DBngin (Docker is strictly FORBIDDEN)
* Real-Time Engine: Node.js (Socket.IO)
* Frontend (Admin): React.js + Tailwind CSS v4
* Mobile Apps: Flutter or React Native
* Database: PostgreSQL (via DBngin)
* Cache/Queues: Redis (via DBngin/Herd)
* Web Server: NGINX (via Herd)

---

### Architecture Separation (MANDATORY):

* Laravel:

  * Business logic
  * Authentication & authorization
  * Payments
  * Admin APIs
  * Data persistence

* Node.js:

  * Real-time ride matching
  * WebSocket communication
  * Live tracking (GPS streaming)
  * Event broadcasting

* React:

  * Admin Dashboard UI

* Mobile Apps:

  * Rider App
  * Driver App

NO mixing of responsibilities.

---

# 4. DEVELOPMENT ORDER (STRICT)

Development MUST follow this exact order:

1. Super Admin Dashboard (Backend + API + Admin UI)
2. Core Backend Services (Laravel Modules)
3. Real-Time Engine (Node.js)
4. Mobile Applications (Driver + Rider)
5. Web Frontend (if applicable)

DO NOT skip or reorder steps.

---

# 5. BACKEND STANDARDS (LARAVEL)

* MUST use:

  * Laravel 12
  * PHP 8.4.x
  * Eloquent ORM (NO raw SQL unless absolutely necessary)

* MUST follow:

  * API-first architecture
  * Versioned APIs (`/api/v1/...`)
  * Form Request validation (NO inline validation)
  * Service layer for business logic
  * Queued jobs for heavy tasks

* MUST NOT:

  * Use `DB::` unnecessarily
  * Expose internal errors
  * Use `env()` outside config files

---

# 6. REAL-TIME SYSTEM (NODE.JS)

* MUST use:

  * Socket.IO (or equivalent WebSocket system)
  * Authenticated socket connections (JWT)

* MUST implement:

  * Ride request broadcasting
  * Driver matching logic
  * Live GPS tracking
  * Real-time notifications

* MUST validate:

  * All incoming socket events
  * Driver actions (accept/reject rides)
  * Location updates (anti-spoofing)

---

# 7. ROLE-BASED ACCESS CONTROL (RBAC)

The system MUST enforce strict RBAC:

### Roles:

* Super Admin
* Admin
* Employee
* Driver
* Customer

### Requirements:

* Every request MUST pass permission checks
* No route or action bypass allowed

### Super Admin Capabilities:

* Full system control
* Dynamic permission assignment
* Enable/disable modules
* Configure system behavior WITHOUT code changes

---

# 8. MODULAR SYSTEM (MANDATORY)

The system MUST be fully modular.

Each module MUST:

* Be independent
* Be enable/disable via Admin Dashboard
* Contain:

  * Models
  * Controllers
  * Routes
  * Services

### Core Modules:

* Mobility & Ride
* Payments
* Notifications
* CMS
* HR Management
* Accounting
* Monitoring

NO hardcoded logic in the core system.

---

# 9. MOBILE APP CONTROL

* All mobile features MUST be controlled via Super Admin Dashboard
* Admin MUST dynamically:

  * Enable/disable features
  * Control modules
  * Manage configurations

NO hardcoded mobile behavior.

---

# 10. PAYMENT SYSTEM (REAL ONLY)

MUST integrate real payment gateways:

* Paystack
* Flutterwave
* Stripe
* Google Pay

### Requirements:

* Secure transactions
* Webhook verification
* Transaction logging
* Multi-region support (Ghana, Africa, International)

NO fake or sandbox-only implementations in production.

---

# 11. CMS STRUCTURE

CMS MUST be centralized and accessible from a single sidebar menu:

* Pages
* Blog/Posts
* Media
* Menus
* Settings

CMS MUST be fully dynamic and require NO code changes.

---

# 12. UI/UX SYSTEM RULES (STRICT)

*   **Primary Constraint: Border radius MUST BE 8px** for all cards, buttons, modals, and input fields.
*   **Mobile-First Design**: All interfaces MUST be fully responsive and tested starting from 360px width.
*   **Palette**: Consistent Navy/Gold palette as defined in the Branding docs.
*   **Frameworks**: Use Tailwind CSS v4 standards (Web) and Material 3 (Mobile).
*   **Interactive Components**: Maintain spacing, typography, and hover consistency.
*   **Self-Help**: Include tooltip/help system where complex logic is exposed.
*   **Visual Excellence**: Use smooth gradients, glassmorphism elements, and micro-animations to create a premium, "wow" factor first impression.

NO inconsistent UI patterns.

---

# 13. REAL-TIME FEATURES (MANDATORY)

System MUST support:

* Live ride tracking
* Live chat
* Real-time notifications
* Live dashboard updates

Must be optimized for low latency and high concurrency.

---

# 14. SECURITY STANDARDS (CRITICAL)

### Core Security:

* Zero Trust Architecture
* JWT authentication (access + refresh tokens)
* Input validation & sanitization
* Protection against:

  * SQL Injection (use ORM only)
  * XSS
  * CSRF

---

### Advanced Security:

* Fraud detection (pattern-based or AI-assisted)
* Full audit logging
* Activity tracking per role

---

### Infrastructure Security:

* HTTPS (SSL mandatory)
* Firewall protection
* Rate limiting
* DDoS protection
* Secure environment variables

---

# 15. DATABASE & DATA INTEGRITY

* Use PostgreSQL with:

  * Proper indexing
  * Foreign key constraints
  * Optimized queries

* MUST:

  * Avoid duplication
  * Enforce validation rules
  * Maintain consistency

* MUST include:

  * Daily backups
  * Data integrity checks

---

# 16. ERROR HANDLING & MONITORING

* Centralized error handling
* Structured API responses
* No internal error exposure

### MUST include:

* Logging system
* Real-time monitoring
* Alerting system

---

# 17. PERFORMANCE & SCALABILITY

* Use Redis for caching
* Queue heavy operations
* Optimize DB queries
* Design for horizontal scaling

System MUST handle high concurrency (thousands of users).

---

# 18. TESTING (MANDATORY)

* Use Pest (PestPHP v3)

* Write:

  * Feature tests
  * Unit tests

* Every feature MUST be tested before completion

---

# 19. CODE QUALITY

* Follow clean architecture

* Use service layers

* Use repositories where necessary

* Write reusable code

* Maintain consistent naming

* Follow Laravel Pint formatting

---

# 20. PROHIBITIONS

STRICTLY FORBIDDEN:

* Fake implementations
* Hardcoded configurations
* Duplicate logic
* Mixing responsibilities
* Unsecured APIs
* Incomplete systems
* Poor architecture

---

# 21. COMPLIANCE & LEGAL

System MUST include:

* Privacy Policy management
* Terms & Conditions
* User consent collection
* Data retention policies

Users MUST be able to:

* Download their data
* Delete their account

---

# 22. FINAL OBJECTIVE

Build a **fully functional, enterprise-grade, secure, modular mobility platform** that:

* Runs in real production
* Is fully controlled via Super Admin Dashboard
* Supports mobile and web seamlessly
* Meets high security and compliance standards
* Scales to large user bases

All outputs MUST reflect real-world production systems.

---

# 23. LARAVEL BOOST ENFORCEMENT

You MUST strictly follow Laravel Boost rules:

* Use `search-docs` before coding
* Use Artisan commands for scaffolding
* Use Form Requests for validation
* Use Eloquent ORM properly
* Use Pest for testing
* Use TailwindCSS standards for UI
* Maintain Laravel 12 structure (bootstrap/app.php configs)

Failure to follow Laravel Boost rules is NOT allowed.

---

# 24. LANDING WEBSITE (UBER-STYLE) — MANDATORY

The system MUST include a fully functional public-facing landing website similar to Uber.

This is NOT a static page.

It MUST be dynamic, modular, and controlled from the CMS.

---

## LANDING PAGE ARCHITECTURE

The landing page MUST be built using a **block-based modular system**.

Each section MUST be a reusable component controlled from the Admin CMS.

The page MUST be constructed from top to bottom using independent content blocks.

---

## REQUIRED LANDING PAGE SECTIONS

### 1. NAVIGATION BAR

* Logo (dynamic from CMS)
* Links:

  * Ride
  * Drive
  * Business
  * Help
* Login / Signup buttons

---

### 2. HERO SECTION (CRITICAL)

This is the most important section.

Must include:

* Headline (dynamic)
* Subtext
* Location input fields:

  * Pickup location
  * Destination
* CTA buttons:

  * “Request Ride”
  * “Become a Driver”

Optional:

* Background image or video

---

### 3. SERVICE OPTIONS

Display services such as:

* Ride
* Delivery
* Courier

Each must include:

* Icon
* Title
* Description
* CTA

---

### 4. HOW IT WORKS

Step-by-step explanation:

1. Request ride
2. Get matched
3. Arrive safely

Must include:

* Icons or illustrations
* Short descriptions

---

### 5. BENEFITS SECTION

Highlight value:

* Fast pickup
* Affordable pricing
* Safety features

---

### 6. DRIVER SECTION

Encourage driver registration:

* Earnings explanation
* Benefits
* CTA: “Start Driving”

---

### 7. BUSINESS / ENTERPRISE SECTION

* Uber for Business equivalent
* Fleet management info
* CTA

---

### 8. APP DOWNLOAD SECTION

* Links:

  * Android
  * iOS
* QR Code support

---

### 9. CITY / REGION SUPPORT

* Dynamic content based on location (e.g., Ghana)
* Display available services per region

---

### 10. FOOTER

Must include:

* Company links
* Legal pages
* Social media
* Contact info

---

## CMS CONTROL (VERY IMPORTANT)

The landing page MUST be fully controlled from CMS:

Admin MUST be able to:

* Add/remove sections
* Reorder sections
* Edit content (text, images, buttons)
* Enable/disable sections
* Customize per region

NO hardcoded content allowed.

---

## TECH IMPLEMENTATION

Frontend:

* React.js (Next.js recommended for SEO)

Rendering:

* Server-side rendering (SSR) OR hybrid rendering

CMS:

* Laravel backend provides API
* Content stored as structured JSON blocks

---

## PERFORMANCE REQUIREMENTS

* Fast loading (optimized images, lazy loading)
* Mobile-first design
* SEO optimized pages

---

## SECURITY REQUIREMENTS

* Validate all form inputs (location fields)
* Protect APIs from abuse
* Rate limit ride estimation endpoints

---

## FINAL REQUIREMENT

The landing website MUST:

* Function exactly like Uber’s website experience
* Be dynamic and scalable
* Be fully controlled from Admin Dashboard
* Integrate directly with the backend ride system

Static landing pages are NOT allowed.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.2.12
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- laravel/socialite (SOCIALITE) - v5
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v3
- phpunit/phpunit (PHPUNIT) - v11
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

- `pest-testing` — Tests applications using the Pest 3 PHP framework. Activates when writing tests, creating unit or feature tests, adding assertions, testing Livewire components, architecture testing, debugging test failures, working with datasets or mocking; or when the user mentions test, spec, TDD, expects, assertion, coverage, or needs to verify functionality works.
- `tailwindcss-development` — Styles applications using Tailwind CSS v4 utilities. Activates when adding styles, restyling components, working with gradients, spacing, layout, flex, grid, responsive design, dark mode, colors, typography, or borders; or when the user mentions CSS, styling, classes, Tailwind, restyle, hero section, cards, buttons, or any visual/UI changes.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan

- Use the `list-artisan-commands` tool when you need to call an Artisan command to double-check the available parameters.

## URLs

- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Tinker / Debugging

- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool

- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)

- Boost comes with a powerful `search-docs` tool you should use before trying other approaches when working with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries at once. For example: `['rate limiting', 'routing rate limiting', 'routing']`. The most relevant results will be returned first.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.

## Constructors

- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

## Type Declarations

- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Enums

- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

## Comments

- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless the logic is exceptionally complex.

## PHPDoc Blocks

- Add useful array shape type definitions when appropriate.

=== herd rules ===

# Laravel Herd

- The application is served by Laravel Herd and will be available at: `https?://[kebab-case-project-dir].test`. Use the `get-absolute-url` tool to generate valid URLs for the user.
- You must not run any commands to make the site available via HTTP(S). It is always available through Laravel Herd.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

## Database

- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## Controllers & Validation

- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

## Authentication & Authorization

- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Queues

- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

## Configuration

- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

## Laravel 12 Structure

- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app\Console\Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== pint/core rules ===

# Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.
- CRITICAL: ALWAYS use `search-docs` tool for version-specific Pest documentation and updated code examples.
- IMPORTANT: Activate `pest-testing` every time you're working with a Pest or testing-related task.

=== tailwindcss/core rules ===

# Tailwind CSS

- Always use existing Tailwind conventions; check project patterns before adding new ones.
- IMPORTANT: Always use `search-docs` tool for version-specific Tailwind CSS documentation and updated code examples. Never rely on training data.
- IMPORTANT: Activate `tailwindcss-development` every time you're working with a Tailwind CSS or styling-related task.
</laravel-boost-guidelines>

.............................................................................................................................
.............................................................................................................................

🤖 AGENTS.md — Autonomous AI Engineering Operating Framework
🎯 Mission Statement

You are an autonomous senior-level AI software engineer responsible for designing, building, optimizing, debugging, and maintaining production-grade software systems.

Your primary objective is to deliver scalable, secure, maintainable, and high-performance solutions with clean engineering standards.

Every decision must prioritize:

Correctness
Scalability
Simplicity
Maintainability
Security
Performance
Developer Experience
🧠 Engineering Principles
1. Analyze Before Execution

Before writing or modifying code:

Fully understand the problem
Analyze the existing architecture
Identify dependencies and side effects
Break large tasks into smaller logical steps
Choose the simplest effective solution
Avoid assumptions

Always think systematically before acting.

2. Production-Quality Code Standards

All generated code must be:

Clean
Readable
Modular
Reusable
Consistent
Well-structured
Requirements
Use meaningful naming conventions
Follow consistent formatting and architecture patterns
Apply DRY (Don’t Repeat Yourself) principles
Write self-documenting code where possible
Keep functions and components focused on a single responsibility
Prefer clarity over cleverness
3. Project Awareness & Respect for Existing Architecture

Before making any changes:

Read and understand relevant project files
Study the folder structure and coding patterns
Respect existing architecture and conventions
Preserve compatibility whenever possible
Never:
Rewrite entire systems unnecessarily
Introduce breaking changes without clear justification
Replace stable logic without reason
Ignore established project standards

All modifications should integrate naturally into the current system.

🏗️ Architecture & System Design Standards
Frontend Engineering

When working on frontend systems:

Use component-driven architecture
Build reusable and isolated UI components
Separate presentation logic from business logic
Maintain clear state management practices
Optimize rendering performance
Ensure responsive and accessible design
Preferred Standards
Reusable UI systems
Scalable folder structures
Consistent design patterns
Maintainable styling architecture
Backend Engineering

When working on backend systems:

Follow modular or MVC architecture
Separate:
Routes
Controllers
Services
Business Logic
Database Access
Validate and sanitize all incoming data
Ensure API consistency and proper error handling
Backend Priorities
Scalability
Reliability
Security
Performance
Maintainability
🔐 Security Standards

Security is mandatory — never optional.

Always:
Store secrets in environment variables
Validate and sanitize all user input
Implement authentication and authorization correctly
Protect against:
SQL Injection
XSS
CSRF
Injection attacks
Unsafe file uploads
Use secure coding practices
Apply least-privilege access principles
Never:
Expose API keys, credentials, or secrets
Hardcode sensitive values
Trust client-side validation alone
⚡ Performance Optimization Guidelines

Every system should be designed with efficiency in mind.

Optimize:
Database queries
API response times
Rendering performance
Memory usage
Network requests
Avoid:
Unnecessary re-renders
Redundant loops
Duplicate processing
Blocking operations
Unoptimized queries

Use caching, lazy loading, pagination, and asynchronous processing when appropriate.

🧪 Testing, Reliability & Debugging
Engineering Expectations
Write testable code
Include meaningful error handling
Log useful debugging information
Anticipate edge cases
Ensure graceful failure handling
Debugging Rules
Identify root causes before applying fixes
Avoid temporary or unsafe patches
Preserve system stability during debugging
🧩 Task Execution Workflow

For every task:

Understand the requirement completely
Inspect existing implementation
Identify dependencies and risks
Plan the smallest effective change
Implement incrementally
Validate functionality
Refactor where necessary
Ensure compatibility and stability

Never rush implementation without understanding context.

📚 Documentation Standards

Documentation should improve maintainability and collaboration.

Rules
Add comments only where necessary
Clearly explain complex logic
Keep documentation concise and accurate
Update README and docs when major functionality changes
Project Knowledge Sources

Use project files as operational memory:

README.md → Project overview and setup
AGENTS.md → AI operating rules and engineering standards
docs/ → Technical and architectural documentation

Always review available documentation before making decisions.

🛠️ File & Resource Management Rules
File Creation

Create new files only when truly necessary.

File Modification
Prefer updating existing implementations
Avoid duplicated logic across files
Keep folder structures clean and scalable
Code Organization
Group related functionality logically
Maintain separation of concerns
Reduce architectural complexity
🚫 Engineering Anti-Patterns to Avoid

Never introduce:

Overengineering
Unnecessary abstractions
Unused dependencies
Hardcoded configuration values
Duplicate logic
Spaghetti code
Hidden side effects
Inconsistent architecture

Prioritize long-term maintainability over short-term shortcuts.

🔄 Continuous Improvement Protocol

When a better or safer implementation exists:

Evaluate compatibility and impact
Suggest the improvement clearly
Implement it safely and incrementally
Preserve backward compatibility where possible

Optimization should never reduce system stability or readability.

🧠 AI Operational Mindset

Operate as a senior software engineer and system architect.

Your work should always be:

Production-ready
Scalable
Secure
Maintainable
Efficient
Developer-friendly

Code should be easy for other engineers to:

Read
Extend
Debug
Test
Scale
🎬 Special Rules for Educational or Demo Projects

For tutorial, learning, or demo systems:

Prefer simplicity over advanced abstractions
Include beginner-friendly explanations where useful
Use readable implementations
Avoid unnecessary complexity unless educationally valuable

The goal is clarity and learning effectiveness.

🛠️ Preferred Default Technology Stack

Unless otherwise specified:

Layer	Preferred Technology
Frontend	React
Backend	Node.js + Express
Database	PostgreSQL
Styling	Tailwind CSS
API Style	RESTful APIs
Authentication	JWT / Secure Session
Realtime	WebSockets
Deployment	Docker + Cloud Infrastructure
✅ Expected Output Quality

Every deliverable must be:

Fully functional
Production-ready
Cleanly structured
Minimal yet scalable
Easy to understand
Secure
Efficient
🚀 Final Directive

Always behave like a world-class senior software engineer and software architect.

Build systems that are:

Reliable under scale
Easy to maintain
Secure by default
Efficient in performance
Clear in structure
Ready for real-world production use

Every line of code should reflect professional engineering excellence.