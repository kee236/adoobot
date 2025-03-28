# ChatPion Modules

## Overview

ChatPion uses a modular architecture to organize its codebase. Each module is a self-contained component that includes its own controllers, models, views, and other resources. This document provides a detailed overview of each module, its purpose, functionality, and how it interacts with other components of the system.

## Module Structure

Each module follows a similar structure:

```
module_name/
├── controllers/       # Controller classes
├── models/            # Model classes (optional)
├── views/             # View templates
├── language/          # Language files (optional)
└── assets/            # Module-specific assets (optional)
```

## Core Modules

### Menu Manager

**Purpose**: Manages navigation menus and custom pages.

**Key Files**:
- `controllers/Menu_manager.php`: Main controller for menu management
- `views/menu_manager.php`: Main view for menu management
- `views/create_page.php`: View for creating custom pages
- `views/update_page.php`: View for updating custom pages
- `views/view_single_page.php`: View for displaying a single page
- `views/custom_page_lists.php`: View for listing custom pages
- `views/menu_block.php`: View for displaying menu blocks

**Functionality**:
- Create, read, update, and delete custom pages
- Manage navigation menus
- Organize pages in menus

### Blog

**Purpose**: Provides blog functionality.

**Key Files**:
- `controllers/Blog.php`: Main controller for blog functionality
- `views/blog.php`: Main view for blog
- `views/blog_post.php`: View for displaying blog posts
- `views/blog_add_post.php`: View for adding blog posts
- `views/blog_edit_post.php`: View for editing blog posts
- `views/blog_category.php`: View for managing blog categories
- `views/blog_tag.php`: View for managing blog tags

**Functionality**:
- Create, read, update, and delete blog posts
- Manage blog categories and tags
- Display blog posts with filtering and pagination

### Simple Support

**Purpose**: Provides a support ticket system.

**Key Files**:
- `controllers/Simplesupport.php`: Main controller for support ticket system
- `views/tickets.php`: View for displaying tickets
- `views/open_ticket.php`: View for opening new tickets
- `views/ticket_reply.php`: View for replying to tickets
- `views/support_category.php`: View for managing support categories
- `views/add_category.php`: View for adding support categories
- `views/edit_category.php`: View for editing support categories

**Functionality**:
- Create, read, update, and delete support tickets
- Manage support categories
- Reply to support tickets

### Visual Flow Builder

**Purpose**: Provides a visual flow builder for creating chatbot flows.

**Key Files**:
- `controllers/Visual_flow_builder.php`: Main controller for visual flow builder
- `views/index.php`: Main view for visual flow builder
- `views/flow_builder_list.php`: View for listing flow builder templates

**Functionality**:
- Create, read, update, and delete chatbot flows
- Visually design conversation flows
- Test and deploy chatbot flows

### Ultrapost

**Purpose**: Provides advanced posting features.

**Key Files**:
- `controllers/Ultrapost.php`: Main controller for ultrapost functionality
- `views/cta_post/`: Views for CTA posts
- `views/carousel_slider_post/`: Views for carousel/slider posts
- `views/poster_menu_block.php`: View for poster menu block

**Functionality**:
- Create, read, update, and delete CTA posts
- Create, read, update, and delete carousel/slider posts
- Schedule and automate posts

### Instagram Poster

**Purpose**: Provides Instagram posting functionality.

**Key Files**:
- `controllers/Instagram_poster.php`: Main controller for Instagram poster
- `views/image_video_post/`: Views for image/video posts

**Functionality**:
- Create, read, update, and delete Instagram posts
- Upload and edit images/videos
- Schedule and automate Instagram posts

### AI Reply

**Purpose**: Provides AI-powered reply functionality.

**Key Files**:
- `controllers/Ai_reply.php`: Main controller for AI reply functionality

**Functionality**:
- Generate AI-powered replies
- Train AI models with custom data
- Automate responses based on AI analysis

### Instagram Bot

**Purpose**: Provides Instagram automation functionality.

**Key Files**:
- `controllers/Instagram_bot.php`: Main controller for Instagram bot functionality

**Functionality**:
- Automate Instagram interactions
- Schedule and manage Instagram activities
- Monitor and analyze Instagram performance

## Module Interactions

Modules interact with each other through various mechanisms:

1. **Direct Function Calls**: One module may call functions from another module
2. **Events and Hooks**: Modules may trigger or listen for events
3. **Shared Data**: Modules may share data through the database or session
4. **API Calls**: Modules may communicate through internal API calls

## Adding New Modules

To add a new module to the system:

1. Create a new directory in the `application/modules` directory
2. Create the necessary subdirectories (controllers, models, views, etc.)
3. Implement the required functionality
4. Register the module in the system (if necessary)

## Best Practices

When working with modules:

1. **Encapsulation**: Keep module functionality self-contained
2. **Loose Coupling**: Minimize dependencies between modules
3. **Consistent Structure**: Follow the established module structure
4. **Documentation**: Document module functionality and interfaces
5. **Testing**: Test module functionality in isolation and integration