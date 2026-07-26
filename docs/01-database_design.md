Step 1 — Migration Commands
<!-- Create a migration: -->
- php artisan make:migration create_projects_table
<!-- Create a model and migration together: -->
- php artisan make:model Project -m
<!-- Create everything at once: -->
- php artisan make:model Project -mfsc
This generates:
    Model
    Migration
    Factory
    Seeder
    Controller
---

Step 2 — Design the Database
app/
 └── Models/
      User.php
      Project.php
      Task.php
      Comment.php

Step 3 - Run the migration
- php artisan migrate

Step 4 — Understand Eloquent ORM
- ORM stand for Object-Relational-Mapping is Eloquent maps database tables to PHP objects by using Model.

Step 5 — Configure Fillable Properties
- $fillable protects against mass-assignment vulnerabilities by allowing only specified fields to be assigned using methods like Model::create().

Step 6 — Build Relationships
---
- User Model
<!-- User Relation with other table -->
public function projects()
{
    return $this->hasMany(Project::class);
}

public function tasks()
{
    return $this->hasMany(Task::class);
}

public function comments()
{
    return $this->hasMany(Comment::class);
}
---

Step 7 - Factories
- Generate fake data for testing.
- php artisan make:factory ProjectFactory --model=Project

Step 8 - Seeder
<!-- Create Seeder -->
- php artisan make:seeder ProjectSeeder
- Example: Project::factory(20)->create();
<!-- Run -->
- php artisan db:seed

<!-- Refresh Everything -->
- php artisan migrate:fresh --seed
