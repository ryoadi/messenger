# Laravel Livewire Realtime Chat App
A simple chat app, built with Laravel Livewire as it's core stack, showcasing ability to build a realtime chat app without relying on a frontend frameworks.

## Features
- Add, edit, delete message
- Realtime message update
- Room creation, room member management
- Direct (1 on 1) room
- Group room (multiple users)

## Tech stack
- PHP 8.4
- Laravel 12
- Livewire 3
- Laravel Folio
- Laravel Reverb
- Flux UI 2
- Tailwind 4
- PestPHP 4
- Laravel Pint
- Larastan/PHPstan
- Laravel Boost

Primary composer dependencies are listed in [composer.json](composer.json).

## Architecture, design patterns & best practices
- Action pattern
- UI - Single-file components
- File path based routing
- Unit testing
- Feature testing
- Laravel code standard

## AI
AI were used on the development for:

- Writing implementation details
- Generating tests
- Debugging issues

All AI suggestions were reviewed and edited by the developer. Tests and static analysis were used to validate the changes.

## Files & places to look
1. [Actions](app/Actions)
2. [Models](app/Models)
3. [Policies](app/Models/Policies)
4. [Migrations](database/migrations)
5. [Web pages](resources/views/pages)
6. [Livewire components](resources/views/livewire)
7. [Tests](tests/Unit)
