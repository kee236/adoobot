# ChatPion API Documentation

## Overview

ChatPion provides a comprehensive API for integrating with external systems and extending its functionality. This document provides a detailed overview of the API endpoints, authentication methods, request/response formats, and examples.

## Authentication

### API Keys

ChatPion uses API keys for authentication. To obtain an API key:

1. Log in to your ChatPion account
2. Navigate to Admin > API Settings
3. Generate a new API key
4. Copy the API key for use in your applications

### Authentication Methods

#### HTTP Header Authentication

Include your API key in the HTTP header of your requests:

```
X-API-Key: your_api_key_here
```

#### Query Parameter Authentication

Include your API key as a query parameter in your requests:

```
https://your-domain.com/api/endpoint?api_key=your_api_key_here
```

## API Endpoints

### Account Management

#### Get Account Information

```
GET /api/account
```

**Description**: Retrieves information about the authenticated account.

**Parameters**: None

**Response**:

```json
{
  "status": "success",
  "data": {
    "id": 123,
    "name": "Example Account",
    "email": "example@example.com",
    "plan": "premium",
    "created_at": "2023-01-01T00:00:00Z",
    "updated_at": "2023-01-01T00:00:00Z"
  }
}
```

### Social Media Management

#### List Social Accounts

```
GET /api/social_accounts
```

**Description**: Retrieves a list of connected social media accounts.

**Parameters**:
- `platform` (optional): Filter by platform (facebook, instagram, etc.)
- `status` (optional): Filter by status (active, inactive)

**Response**:

```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "platform": "facebook",
      "name": "Example Page",
      "access_token": "********",
      "status": "active",
      "connected_at": "2023-01-01T00:00:00Z"
    },
    {
      "id": 2,
      "platform": "instagram",
      "name": "Example Instagram",
      "access_token": "********",
      "status": "active",
      "connected_at": "2023-01-01T00:00:00Z"
    }
  ]
}
```

#### Connect Social Account

```
POST /api/social_accounts
```

**Description**: Connects a new social media account.

**Parameters**:
- `platform` (required): The platform to connect (facebook, instagram, etc.)
- `access_token` (required): The access token for the account
- `name` (optional): A custom name for the account

**Request**:

```json
{
  "platform": "facebook",
  "access_token": "your_access_token_here",
  "name": "My Facebook Page"
}
```

**Response**:

```json
{
  "status": "success",
  "data": {
    "id": 3,
    "platform": "facebook",
    "name": "My Facebook Page",
    "access_token": "********",
    "status": "active",
    "connected_at": "2023-01-01T00:00:00Z"
  }
}
```

### Subscriber Management

#### List Subscribers

```
GET /api/subscribers
```

**Description**: Retrieves a list of subscribers.

**Parameters**:
- `page` (optional): Page number for pagination
- `limit` (optional): Number of results per page
- `platform` (optional): Filter by platform
- `tag` (optional): Filter by tag

**Response**:

```json
{
  "status": "success",
  "data": {
    "total": 100,
    "page": 1,
    "limit": 10,
    "subscribers": [
      {
        "id": 1,
        "platform": "facebook",
        "platform_id": "12345",
        "name": "John Doe",
        "email": "john@example.com",
        "tags": ["customer", "newsletter"],
        "subscribed_at": "2023-01-01T00:00:00Z"
      },
      // More subscribers...
    ]
  }
}
```

#### Add Subscriber

```
POST /api/subscribers
```

**Description**: Adds a new subscriber.

**Parameters**:
- `platform` (required): The platform of the subscriber
- `platform_id` (required): The platform ID of the subscriber
- `name` (optional): The name of the subscriber
- `email` (optional): The email of the subscriber
- `tags` (optional): Tags to assign to the subscriber

**Request**:

```json
{
  "platform": "facebook",
  "platform_id": "12345",
  "name": "John Doe",
  "email": "john@example.com",
  "tags": ["customer", "newsletter"]
}
```

**Response**:

```json
{
  "status": "success",
  "data": {
    "id": 101,
    "platform": "facebook",
    "platform_id": "12345",
    "name": "John Doe",
    "email": "john@example.com",
    "tags": ["customer", "newsletter"],
    "subscribed_at": "2023-01-01T00:00:00Z"
  }
}
```

### Messaging

#### Send Message

```
POST /api/messages
```

**Description**: Sends a message to a subscriber.

**Parameters**:
- `subscriber_id` (required): The ID of the subscriber
- `message` (required): The message to send
- `type` (optional): The type of message (text, image, video, etc.)
- `attachment` (optional): URL or base64-encoded attachment

**Request**:

```json
{
  "subscriber_id": 1,
  "message": "Hello, world!",
  "type": "text"
}
```

**Response**:

```json
{
  "status": "success",
  "data": {
    "id": 1,
    "subscriber_id": 1,
    "message": "Hello, world!",
    "type": "text",
    "sent_at": "2023-01-01T00:00:00Z",
    "status": "sent"
  }
}
```

#### List Messages

```
GET /api/messages
```

**Description**: Retrieves a list of messages.

**Parameters**:
- `subscriber_id` (optional): Filter by subscriber ID
- `page` (optional): Page number for pagination
- `limit` (optional): Number of results per page
- `status` (optional): Filter by status (sent, delivered, read, failed)

**Response**:

```json
{
  "status": "success",
  "data": {
    "total": 100,
    "page": 1,
    "limit": 10,
    "messages": [
      {
        "id": 1,
        "subscriber_id": 1,
        "message": "Hello, world!",
        "type": "text",
        "sent_at": "2023-01-01T00:00:00Z",
        "status": "sent"
      },
      // More messages...
    ]
  }
}
```

### Broadcasting

#### Create Broadcast

```
POST /api/broadcasts
```

**Description**: Creates a new broadcast.

**Parameters**:
- `name` (required): The name of the broadcast
- `message` (required): The message to broadcast
- `type` (optional): The type of message (text, image, video, etc.)
- `attachment` (optional): URL or base64-encoded attachment
- `target` (optional): Target criteria for the broadcast
- `schedule` (optional): Schedule for the broadcast

**Request**:

```json
{
  "name": "Newsletter Broadcast",
  "message": "Check out our latest newsletter!",
  "type": "text",
  "target": {
    "tags": ["newsletter"],
    "platforms": ["facebook"]
  },
  "schedule": {
    "time": "2023-01-01T12:00:00Z",
    "timezone": "UTC"
  }
}
```

**Response**:

```json
{
  "status": "success",
  "data": {
    "id": 1,
    "name": "Newsletter Broadcast",
    "message": "Check out our latest newsletter!",
    "type": "text",
    "target": {
      "tags": ["newsletter"],
      "platforms": ["facebook"]
    },
    "schedule": {
      "time": "2023-01-01T12:00:00Z",
      "timezone": "UTC"
    },
    "status": "scheduled",
    "created_at": "2023-01-01T00:00:00Z"
  }
}
```

### Flow Builder

#### List Flows

```
GET /api/flows
```

**Description**: Retrieves a list of chatbot flows.

**Parameters**:
- `page` (optional): Page number for pagination
- `limit` (optional): Number of results per page
- `status` (optional): Filter by status (active, inactive)

**Response**:

```json
{
  "status": "success",
  "data": {
    "total": 10,
    "page": 1,
    "limit": 10,
    "flows": [
      {
        "id": 1,
        "name": "Welcome Flow",
        "description": "Welcome message for new subscribers",
        "status": "active",
        "created_at": "2023-01-01T00:00:00Z",
        "updated_at": "2023-01-01T00:00:00Z"
      },
      // More flows...
    ]
  }
}
```

#### Get Flow

```
GET /api/flows/{id}
```

**Description**: Retrieves a specific chatbot flow.

**Parameters**:
- `id` (required): The ID of the flow

**Response**:

```json
{
  "status": "success",
  "data": {
    "id": 1,
    "name": "Welcome Flow",
    "description": "Welcome message for new subscribers",
    "status": "active",
    "nodes": [
      {
        "id": "start",
        "type": "start",
        "next": "message1"
      },
      {
        "id": "message1",
        "type": "message",
        "content": "Welcome to our chatbot!",
        "next": "question1"
      },
      {
        "id": "question1",
        "type": "question",
        "content": "How can I help you today?",
        "options": [
          {
            "label": "Learn more",
            "value": "learn",
            "next": "message2"
          },
          {
            "label": "Contact support",
            "value": "support",
            "next": "message3"
          }
        ]
      },
      {
        "id": "message2",
        "type": "message",
        "content": "Here's more information about our product...",
        "next": null
      },
      {
        "id": "message3",
        "type": "message",
        "content": "Please contact our support team at support@example.com",
        "next": null
      }
    ],
    "created_at": "2023-01-01T00:00:00Z",
    "updated_at": "2023-01-01T00:00:00Z"
  }
}
```

### Analytics

#### Get Dashboard Analytics

```
GET /api/analytics/dashboard
```

**Description**: Retrieves analytics data for the dashboard.

**Parameters**:
- `start_date` (optional): Start date for the analytics data
- `end_date` (optional): End date for the analytics data
- `platform` (optional): Filter by platform

**Response**:

```json
{
  "status": "success",
  "data": {
    "subscribers": {
      "total": 1000,
      "new": 100,
      "growth": 10
    },
    "messages": {
      "total": 5000,
      "sent": 4500,
      "delivered": 4000,
      "read": 3500
    },
    "engagement": {
      "clicks": 1000,
      "replies": 500,
      "conversions": 100
    }
  }
}
```

## Error Handling

### Error Responses

When an error occurs, the API returns a JSON response with an error message and HTTP status code:

```json
{
  "status": "error",
  "message": "Invalid API key",
  "code": 401
}
```

### Common Error Codes

- `400`: Bad Request - The request was malformed or missing required parameters
- `401`: Unauthorized - Invalid or missing API key
- `403`: Forbidden - The API key does not have permission to access the requested resource
- `404`: Not Found - The requested resource was not found
- `429`: Too Many Requests - Rate limit exceeded
- `500`: Internal Server Error - An error occurred on the server

## Rate Limiting

The API enforces rate limiting to prevent abuse. The current limits are:

- 100 requests per minute per API key
- 5,000 requests per day per API key

When a rate limit is exceeded, the API returns a `429 Too Many Requests` response with a `Retry-After` header indicating when the client can retry the request.

## Webhooks

ChatPion supports webhooks for real-time notifications of events. To configure webhooks:

1. Log in to your ChatPion account
2. Navigate to Admin > API Settings > Webhooks
3. Add a new webhook URL
4. Select the events to subscribe to

### Webhook Events

- `subscriber.created`: A new subscriber was created
- `subscriber.updated`: A subscriber was updated
- `message.sent`: A message was sent
- `message.delivered`: A message was delivered
- `message.read`: A message was read
- `broadcast.started`: A broadcast started
- `broadcast.completed`: A broadcast completed

### Webhook Payload

```json
{
  "event": "subscriber.created",
  "timestamp": "2023-01-01T00:00:00Z",
  "data": {
    "id": 1,
    "platform": "facebook",
    "platform_id": "12345",
    "name": "John Doe",
    "email": "john@example.com",
    "tags": ["customer", "newsletter"],
    "subscribed_at": "2023-01-01T00:00:00Z"
  }
}
```

## SDKs and Libraries

ChatPion provides official SDKs for popular programming languages:

- [JavaScript/Node.js](https://github.com/chatpion/chatpion-js)
- [PHP](https://github.com/chatpion/chatpion-php)
- [Python](https://github.com/chatpion/chatpion-python)
- [Ruby](https://github.com/chatpion/chatpion-ruby)

## Examples

### JavaScript/Node.js

```javascript
const ChatPion = require('chatpion');

const client = new ChatPion({
  apiKey: 'your_api_key_here'
});

// Get account information
client.getAccount()
  .then(account => {
    console.log(account);
  })
  .catch(error => {
    console.error(error);
  });

// Send a message
client.sendMessage({
  subscriber_id: 1,
  message: 'Hello, world!',
  type: 'text'
})
  .then(message => {
    console.log(message);
  })
  .catch(error => {
    console.error(error);
  });
```

### PHP

```php
<?php

require_once 'vendor/autoload.php';

$client = new ChatPion\Client('your_api_key_here');

// Get account information
try {
    $account = $client->getAccount();
    print_r($account);
} catch (Exception $e) {
    echo $e->getMessage();
}

// Send a message
try {
    $message = $client->sendMessage([
        'subscriber_id' => 1,
        'message' => 'Hello, world!',
        'type' => 'text'
    ]);
    print_r($message);
} catch (Exception $e) {
    echo $e->getMessage();
}
```

### Python

```python
import chatpion

client = chatpion.Client(api_key='your_api_key_here')

# Get account information
try:
    account = client.get_account()
    print(account)
except Exception as e:
    print(e)

# Send a message
try:
    message = client.send_message(
        subscriber_id=1,
        message='Hello, world!',
        type='text'
    )
    print(message)
except Exception as e:
    print(e)
```

## Best Practices

1. **Use HTTPS**: Always use HTTPS for API requests to ensure secure communication
2. **Handle Rate Limiting**: Implement exponential backoff when rate limits are encountered
3. **Validate Input**: Validate user input before sending it to the API
4. **Handle Errors**: Implement proper error handling for API responses
5. **Use Webhooks**: Use webhooks for real-time notifications instead of polling
6. **Cache Responses**: Cache API responses when appropriate to reduce API calls
7. **Use SDKs**: Use official SDKs when available for easier integration