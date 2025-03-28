# ChatPion Architecture

## Overview

ChatPion is built on the CodeIgniter PHP framework, following the Model-View-Controller (MVC) architectural pattern with additional modular components. This document provides a detailed overview of the application's architecture, including its directory structure, components, and how they interact.

## Directory Structure

```
/
├── application/            # Core application code
│   ├── cache/              # Cache files
│   ├── config/             # Configuration files
│   ├── controllers/        # Controller classes
│   ├── core/               # Core system extensions
│   ├── helpers/            # Helper functions
│   ├── hooks/              # Hook functions
│   ├── language/           # Language files
│   ├── libraries/          # Custom libraries
│   ├── logs/               # Log files
│   ├── models/             # Model classes
│   ├── modules/            # Modular components
│   ├── third_party/        # Third-party libraries
│   └── views/              # View templates
├── assets/                 # Static assets
├── ci/                     # CI/CD configuration
├── documentation/          # User documentation
├── download/               # Downloaded files
├── js/                     # JavaScript files
├── member/                 # Member-related files
├── plugins/                # Plugin extensions
├── system/                 # CodeIgniter framework
├── upload/                 # Uploaded files
├── upload_caster/          # Upload caster files
├── .htaccess               # Apache configuration
├── composer.json           # Composer dependencies
├── composer.lock           # Composer lock file
└── index.php               # Application entry point
```

## Core Components

### MVC Architecture

ChatPion follows the Model-View-Controller (MVC) architectural pattern:

- **Models**: Handle data access and business logic
- **Views**: Handle presentation and user interface
- **Controllers**: Handle user input and coordinate between models and views

### Modular Architecture

In addition to the standard MVC architecture, ChatPion uses a modular approach for organizing code. Each module is a self-contained component that includes its own controllers, models, views, and other resources. This modular approach allows for better code organization, reusability, and maintainability.

The main modules include:

- **menu_manager**: Manages navigation menus and custom pages
- **blog**: Provides blog functionality
- **simplesupport**: Provides a support ticket system
- **visual_flow_builder**: Provides a visual flow builder for creating chatbot flows
- **ultrapost**: Provides advanced posting features
- **instagram_poster**: Provides Instagram posting functionality
- **ai_reply**: Provides AI-powered reply functionality
- **instagram_bot**: Provides Instagram automation functionality

### Libraries and Helpers

ChatPion uses various libraries and helpers to provide common functionality:

- **Libraries**: Reusable classes that provide specific functionality
- **Helpers**: Collections of functions that assist with common tasks

### Configuration

Configuration files are stored in the `application/config` directory and include:

- **config.php**: Main CodeIgniter configuration
- **database.php**: Database configuration
- **routes.php**: URL routing configuration
- **autoload.php**: Autoloading configuration
- **constants.php**: Application constants
- **hooks.php**: Hook configuration
- **pusher.php**: Configuration for Pusher (real-time messaging)
- **grocery_crud.php**: Configuration for Grocery CRUD (a CRUD generator library)
- **my_config.php**, **package_config.php**, **frontend_config.php**: Custom configuration files

## Data Flow

1. **Request**: A user makes a request to the application
2. **Routing**: The request is routed to the appropriate controller
3. **Controller**: The controller processes the request, interacts with models, and loads views
4. **Model**: The model interacts with the database and performs business logic
5. **View**: The view renders the data provided by the controller
6. **Response**: The response is sent back to the user

## Integration Points

ChatPion integrates with various external services and APIs:

- **Facebook API**: For Facebook integration
- **Instagram API**: For Instagram integration
- **Google API**: For Google integration
- **Pusher**: For real-time messaging
- **Payment Gateways**: For payment processing (PayPal, Stripe)
- **Email Services**: For email sending and processing

## Security

ChatPion implements various security measures:

- **Authentication**: User authentication and authorization
- **Input Validation**: Validation of user input
- **CSRF Protection**: Protection against Cross-Site Request Forgery
- **XSS Protection**: Protection against Cross-Site Scripting
- **SQL Injection Protection**: Protection against SQL Injection

## Scalability

ChatPion is designed to be scalable:

- **Modular Architecture**: Allows for easy addition of new features
- **Caching**: Improves performance by caching frequently accessed data
- **Database Optimization**: Optimized database queries and structure
- **Asynchronous Processing**: Background processing for time-consuming tasks