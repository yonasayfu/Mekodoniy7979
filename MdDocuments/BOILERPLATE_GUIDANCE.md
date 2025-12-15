# Boilerplate Guidance for AI Agents

This document provides a guide for AI agents to understand the structure, conventions, and styling of this Laravel boilerplate. The goal is to ensure consistency when adding new features.

## 1. Backend Architecture

The backend follows a "Clean Architecture" approach, with a focus on separating domain logic from the application and framework layers.

### Key Concepts

*   **Modules**: The `app/Domain` directory is organized into modules, where each module represents a specific business domain (e.g., `Users`, `Staff`).
*   **Services**: Domain logic is encapsulated in `Service` classes (e.g., `app/Domain/Users/UserService.php`). Services are responsible for orchestrating business logic and interacting with the database.
*   **Data Transfer Objects (DTOs)**: DTOs are used to pass data between layers. They are simple, immutable objects that represent a specific piece of data. We use the `spatie/laravel-data` package for DTOs.
*   **Controllers**: Controllers are responsible for handling HTTP requests and returning responses. They should be as "thin" as possible, delegating the business logic to the services.
*   **Requests**: Form requests are used to validate incoming data.
*   **Resources**: API resources are used to transform models into JSON responses.

### `make:module` Command

A custom Artisan command `php artisan make:module {ModuleName}` is provided to scaffold a new module with the correct structure. This command creates:
*   A Controller
*   A Service
*   A DTO
*   A Request class
*   A Resource class

The stubs for these files are located in the `stubs/module` directory.

## 2. Frontend Architecture & Styling

The frontend is built with **Vue 3** and **Inertia.js**. Styling is done with **Tailwind CSS**.

### Styling Conventions

*   **Tailwind CSS**: All styling is done using Tailwind CSS utility classes. Avoid writing custom CSS whenever possible. The Tailwind configuration is in `tailwind.config.js` and the main CSS file is `resources/css/app.css`.
*   **UI Components**: The boilerplate uses `shadcn-vue` for UI components, which are located in `resources/js/components/ui`. These components are highly customizable and should be used whenever possible to maintain a consistent look and feel.
*   **Buttons**: Use the `<Button>` component from `resources/js/components/ui/button`. It has several variants (`default`, `destructive`, `outline`, `secondary`, `ghost`, `link`) and sizes (`default`, `sm`, `lg`, `icon`).
*   **Forms**:
    *   **Inputs**: Use the `<Input>` component from `resources/js/components/ui/input`.
    *   **Labels**: Use the `<Label>` component from `resources/js/components/ui/label`.
    *   **Form Layout**: Forms are typically built within a `<GlassCard>` component.
*   **Tables**: Use the `<Table>` component and its related components (`<TableHeader>`, `<TableBody>`, etc.) from `resources/js/components/ui/table`.
*   **Cards**: The `<GlassCard>` component (`resources/js/components/GlassCard.vue`) is a custom component that provides a consistent "glassmorphism" effect. Use it to wrap sections of content.
*   **Icons**: The boilerplate uses the `lucide-vue-next` library for icons.

### File Structure

*   **Pages**: Inertia pages are located in `resources/js/pages`. Each page is a `.vue` file.
*   **Layouts**: Layouts are in `resources/js/layouts`. The main application layout is `AppLayout.vue`.
*   **Components**: Reusable Vue components are in `resources/js/components`.
*   **Composables**: Reusable Vue composables are in `resources/js/composables`.

## 3. How to Add a New Feature

When adding a new feature, please follow these steps to maintain consistency:

1.  **Create a new module**: Use `php artisan make:module {ModuleName}` to create the backend structure.
2.  **Add migrations**: Create new migrations for any new database tables.
3.  **Implement the domain logic**: Add the business logic to the `Service` class.
4.  **Implement the controller**: Wire up the controller to the service and return Inertia responses.
5.  **Create the Vue pages**: Create the necessary `.vue` pages in `resources/js/pages`.
6.  **Use existing UI components**: Use the components from `resources/js/components/ui` to build the UI.
7.  **Add routes**: Add the new routes to `routes/web.php` and `routes/api.php`.
8.  **Add tests**: Add feature and unit tests for the new functionality.
