# Task Management API Documentation

## Base URL
```
http://localhost/taskmanagement/api
```

## Authentication
Currently no authentication required. For production, consider implementing API tokens.

## Rate Limiting
All API routes are rate limited to prevent abuse.

---

## Endpoints

### 1. Get All Tasks
```
GET /api/tasks
Query Parameters:
- filter: all|completed|incomplete (optional)
```

### 2. Get Single Task
```
GET /api/tasks/{id}
```

### 3. Create Task
```
POST /api/tasks
Body:
{
  "title": "string (required)",
  "description": "string (optional)",
  "completed": "boolean (optional)",
  "order": "integer (optional)"
}
```

### 4. Update Task
```
PUT/PATCH /api/tasks/{id}
Body:
{
  "title": "string (optional)",
  "description": "string (optional)",
  "completed": "boolean (optional)",
  "order": "integer (optional)"
}
```

### 5. Delete Task
```
DELETE /api/tasks/{id}
```

### 6. Reorder Tasks
```
POST /api/tasks/reorder
Body:
{
  "order": [1, 2, 3, 4]
}
```

### 7. Toggle Task Status
```
POST /api/tasks/{id}/toggle
Body:
{
  "completed": true|false
}
```

### 8. DataTable Endpoint
```
GET /api/tasks/datatable
Query Parameters:
- draw: integer (DataTable draw counter)
- start: integer (pagination start)
- length: integer (records per page)
- search[value]: string (search term)
- order[0][column]: integer (sort column)
- order[0][dir]: asc|desc (sort direction)
- filter: all|completed|incomplete
```

---

## Response Format

### Standard Success Response
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": {
    "id": 1,
    "title": "Task Title",
    "description": "Task Description",
    "completed": false,
    "order": 1,
    "created_at": "2024-01-01T12:00:00.000000Z",
    "updated_at": "2024-01-01T12:00:00.000000Z"
  },
  "status_code": 200,
  "timestamp": "2024-01-01T12:00:00.000000Z"
}
```

### Collection Response
```json
{
  "success": true,
  "message": "Tasks retrieved successfully",
  "data": [
    {
      "id": 1,
      "title": "Task 1",
      "description": "Description 1",
      "completed": false,
      "order": 1,
      "created_at": "2024-01-01T12:00:00.000000Z",
      "updated_at": "2024-01-01T12:00:00.000000Z"
    }
  ],
  "status_code": 200,
  "timestamp": "2024-01-01T12:00:00.000000Z"
}
```

### Paginated Response
```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": [
    {
      "id": 1,
      "title": "Task 1",
      "description": "Description 1",
      "completed": false,
      "order": 1,
      "created_at": "2024-01-01T12:00:00.000000Z",
      "updated_at": "2024-01-01T12:00:00.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 10,
    "total": 25,
    "from": 1,
    "to": 10,
    "has_more_pages": true
  },
  "status_code": 200,
  "timestamp": "2024-01-01T12:00:00.000000Z"
}
```

### Validation Error Response (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "title": ["The title field is required."],
    "description": ["The description must be a string."]
  },
  "status_code": 422,
  "timestamp": "2024-01-01T12:00:00.000000Z"
}
```

### Not Found Error Response (404)
```json
{
  "success": false,
  "message": "Resource not found",
  "status_code": 404,
  "timestamp": "2024-01-01T12:00:00.000000Z"
}
```

### Server Error Response (500)
```json
{
  "success": false,
  "message": "Internal server error",
  "status_code": 500,
  "timestamp": "2024-01-01T12:00:00.000000Z"
}
```

---

## Status Codes
- **200**: Success (GET, PUT, PATCH, DELETE)
- **201**: Created (POST)
- **400**: Bad Request
- **401**: Unauthorized
- **403**: Forbidden
- **404**: Not Found
- **422**: Validation Error
- **429**: Too Many Requests (Rate Limited)
- **500**: Internal Server Error

## HTTP Status Code Usage

### Success Responses
- **200 OK**: Successful GET, PUT, PATCH, DELETE operations
- **201 Created**: Successful POST operations (resource creation)

### Client Error Responses
- **400 Bad Request**: Invalid request format
- **401 Unauthorized**: Authentication required
- **403 Forbidden**: Access denied
- **404 Not Found**: Resource not found
- **422 Unprocessable Entity**: Validation errors

### Server Error Responses
- **500 Internal Server Error**: Server-side errors
- **429 Too Many Requests**: Rate limit exceeded
