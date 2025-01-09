# SKILLMARTONE-BE API Documentation

## Overview
This is the backend API for the Skillmartone application. The API provides endpoints for managing roles, users, and other resources.

## Base URL
```
http://localhost/skillmartone-be/index.php
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

### Requirements
- PHP >= 7.4
- MySQL >= 5.7
- Web server (Apache/Nginx)