# SKILLMARTONE-BE API Documentation

## Overview
This is the backend API for the Skillmartone application. The API provides endpoints for managing roles, users, and other resources.

## Base URL
```
http://localhost/skillmartone-be
```

## API Response Format
All API responses follow this standard format:
```json
{
  "meta": {
    "success": true,
    "code": 200,
    "message": "Success message"
  },
  "data": {
    "page_data": {},
    "page_info": {
      "current_page": 1,
      "total_pages": 5,
      "total_records": 50,
      "limit": 10
    }
  }
}
```

## Authentication
[To be implemented]

## Role Management API Endpoints

### Get All Roles
Get a paginated list of all roles.

- **URL**: `?route=roles`
- **Method**: `GET`
- **URL Params**:
  - `page` (optional): Page number for pagination (default: 1)
  - `limit` (optional): Items per page (default: 10)

#### Success Response
```json
{
  "meta": {
    "success": true,
    "code": 200,
    "message": "Success fetch roles data"
  },
  "data": {
    "page_data": [
      {
        "id": 1,
        "name": "admin",
        "description": "Administrator Role"
      }
    ],
    "page_info": {
      "current_page": 1,
      "total_pages": 5,
      "total_records": 50,
      "limit": 10
    }
  }
}
```

### Get Single Role
Get detailed information about a specific role.

- **URL**: `?route=roles&action=show&id=1`
- **Method**: `GET`
- **URL Params**:
  - `id` (required): Role ID

#### Success Response
```json
{
  "meta": {
    "success": true,
    "code": 200,
    "message": "Success fetch role detail"
  },
  "data": {
    "page_data": {
      "id": 1,
      "name": "admin",
      "description": "Administrator Role"
    },
    "page_info": {}
  }
}
```

### Create Role
Create a new role.

- **URL**: `?route=roles`
- **Method**: `POST`
- **Headers**: 
  - Content-Type: application/json
- **Body**:
```json
{
    "name": "admin",
    "description": "Administrator Role"
}
```

#### Success Response
```json
{
  "meta": {
    "success": true,
    "code": 201,
    "message": "Role created successfully"
  },
  "data": {
    "page_data": {
      "id": 1
    },
    "page_info": {}
  }
}
```

### Update Role
Update an existing role.

- **URL**: `?route=roles&id=1`
- **Method**: `PUT`
- **Headers**: 
  - Content-Type: application/json
- **URL Params**:
  - `id` (required): Role ID
- **Body**:
```json
{
    "name": "admin updated",
    "description": "Administrator Role Updated"
}
```

#### Success Response
```json
{
  "meta": {
    "success": true,
    "code": 200,
    "message": "Role updated successfully"
  },
  "data": {
    "page_data": {},
    "page_info": {}
  }
}
```

### Delete Role
Delete a role.

- **URL**: `?route=roles&id=1`
- **Method**: `DELETE`
- **URL Params**:
  - `id` (required): Role ID

#### Success Response
```json
{
  "meta": {
    "success": true,
    "code": 200,
    "message": "Role deleted successfully"
  },
  "data": {
    "page_data": {},
    "page_info": {}
  }
}
```

## Error Responses
All endpoints may return these error responses:

### 400 Bad Request
```json
{
  "meta": {
    "success": false,
    "code": 400,
    "message": "Error message describing the issue"
  },
  "data": {
    "page_data": {},
    "page_info": {}
  }
}
```

### 404 Not Found
```json
{
  "meta": {
    "success": false,
    "code": 404,
    "message": "Resource not found"
  },
  "data": {
    "page_data": {},
    "page_info": {}
  }
}
```

### 500 Server Error
```json
{
  "meta": {
    "success": false,
    "code": 500,
    "message": "Internal server error message"
  },
  "data": {
    "page_data": {},
    "page_info": {}
  }
}
```

## Development Setup

### Requirements
- PHP >= 7.4
- MySQL >= 5.7
- Apache/Nginx web server

### Installation
1. Clone the repository
```bash
git clone https://github.com/yourusername/skillmartone-be.git
```

2. Set up your database configuration in `api/config/database.php`

3. Import the database schema (if provided)

4. Configure your web server to point to the project directory

### Database Schema
[To be added]

## Contributing
[Guidelines for contributing to the project]

## License
[Your chosen license]