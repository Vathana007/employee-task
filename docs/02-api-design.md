# Employee Task API Design

This document serves as the complete reference for all available API endpoints, including all CRUD operations (Create, Read All, Read Specific, Update, Delete) for every resource in the system.

## Global Configuration
- **Base URL**: `http://127.0.0.1:8000/api`
- **Headers Required (for all endpoints below except Register & Login)**:
  - `Accept: application/json`
  - `Authorization: Bearer <your_access_token>`
  - `Content-Type: application/json` *(required when sending a JSON body)*

---

## 1. Authentication

### Register a New User
- **Method**: `POST`
- **Endpoint**: `/register`
- **JSON Body**:
```json
{
    "name": "Jane Doe",
    "email": "jane@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

### Login
- **Method**: `POST`
- **Endpoint**: `/login`
- **JSON Body**:
```json
{
    "email": "jane@example.com",
    "password": "password123"
}
```

### Get Current Profile
- **Method**: `GET`
- **Endpoint**: `/profile`
- **JSON Body**: *(none)*

### Logout
- **Method**: `POST`
- **Endpoint**: `/logout`
- **JSON Body**: *(none)*

---

## 2. Users

### Read All Users
- **Method**: `GET`
- **Endpoint**: `/users`
- **JSON Body**: *(none)*

### Read Specific User
- **Method**: `GET`
- **Endpoint**: `/users/{id}`
- **JSON Body**: *(none)*

### Create User
- **Method**: `POST`
- **Endpoint**: `/users`
- **JSON Body**:
```json
{
    "name": "John Smith",
    "email": "john@example.com",
    "password": "password123"
}
```

### Update User
- **Method**: `PUT` (or `PATCH`)
- **Endpoint**: `/users/{id}`
- **JSON Body**:
```json
{
    "name": "John Smith Updated",
    "email": "john.updated@example.com"
}
```

### Delete User
- **Method**: `DELETE`
- **Endpoint**: `/users/{id}`
- **JSON Body**: *(none)*

---

## 3. Projects

### Read All Projects
- **Method**: `GET`
- **Endpoint**: `/projects`
- **JSON Body**: *(none)*

### Read Specific Project
- **Method**: `GET`
- **Endpoint**: `/projects/{id}`
- **JSON Body**: *(none)*

### Create Project
- **Method**: `POST`
- **Endpoint**: `/projects`
- **JSON Body**:
```json
{
    "name": "Q3 Marketing Campaign",
    "description": "Design and execute the marketing campaign for Q3.",
    "status": "pending"
}
```
*(Status options: `pending`, `in_progress`, `completed`)*

### Update Project
- **Method**: `PUT`
- **Endpoint**: `/projects/{id}`
- **JSON Body**:
```json
{
    "status": "in_progress",
    "description": "Updated project description."
}
```

### Delete Project
- **Method**: `DELETE`
- **Endpoint**: `/projects/{id}`
- **JSON Body**: *(none)*

---

## 4. Tasks

### Read All Tasks
- **Method**: `GET`
- **Endpoint**: `/tasks`
- **JSON Body**: *(none)*

### Read Specific Task
- **Method**: `GET`
- **Endpoint**: `/tasks/{id}`
- **JSON Body**: *(none)*

### Create Task
- **Method**: `POST`
- **Endpoint**: `/tasks`
- **JSON Body**:
```json
{
    "project_id": 1,
    "user_id": 2,
    "title": "Design the landing page",
    "description": "Create Figma mockups for the new landing page design.",
    "priority": "high",
    "status": "pending",
    "deadline": "2026-08-30"
}
```
*(Priority options: `low`, `medium`, `high`. Status options: `pending`, `in_progress`, `completed`)*

### Update Task
- **Method**: `PUT`
- **Endpoint**: `/tasks/{id}`
- **JSON Body**:
```json
{
    "status": "in_progress",
    "priority": "medium",
    "description": "Updated task instructions."
}
```

### Delete Task
- **Method**: `DELETE`
- **Endpoint**: `/tasks/{id}`
- **JSON Body**: *(none)*

---

## 5. Comments

### Read All Comments
- **Method**: `GET`
- **Endpoint**: `/comments`
- **JSON Body**: *(none)*

### Read Specific Comment
- **Method**: `GET`
- **Endpoint**: `/comments/{id}`
- **JSON Body**: *(none)*

### Create Comment
- **Method**: `POST`
- **Endpoint**: `/comments`
- **JSON Body**:
```json
{
    "task_id": 1,
    "user_id": 2,
    "comment": "I have started working on the Figma mockups."
}
```

### Update Comment
- **Method**: `PUT`
- **Endpoint**: `/comments/{id}`
- **JSON Body**:
```json
{
    "comment": "I have completed the Figma mockups, ready for review."
}
```

### Delete Comment
- **Method**: `DELETE`
- **Endpoint**: `/comments/{id}`
- **JSON Body**: *(none)*
