## Laravel Map

A modern web application built with Laravel 12, Inertia.js (Vue 3), and Tailwind CSS, featuring dynamic business locations on a map with server-side spatial filtering.

### Features
- **Authentication**: Secure Login/Register flow (Redirects directly to map).
- **Map Bound Filtering**: Automatically fetches and updates business markers based on the current map view (panning/zooming).
- **Responsive Sidebar**: Google Maps-style sidebar with list/detail views, mobile-optimized as a bottom sheet.
- **Dynamic Assets**: Custom markers based on business categories and image gallery.
- **Lazy Loading**: Map engines are loaded asynchronously to optimize performance.

### Installation & Setup 

1.  **Clone the Repository**

    ```bash
    git clone https://github.com/AungPhyoThant224/Map-Comparison.git
    cd Map-Comparison
    ```

2.  **Install PHP Dependencies**
    
    ```bash
    composer install
    ```

3.  **Install Frontend Dependencies**

    ```bash
    npm install
    ```

4.  **Environment Configuration**

    - Copy the example environment file and generate your application key:

    </br>

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

    *Note: Ensure your .env is configured with your local database credentials (DB_DATABASE, DB_USERNAME, DB_PASSWORD).*

5. **Database Migration & Seeding**

    - This project includes a custom seeder that populates 10 businesses around the center of Yangon.

    </br>

    ```bash
    php artisan migrate --seed
    ```

6. **Linking Storage**

    - To ensure custom markers (PNG files) are accessible:

    </br>

    ```bash
    php artisan storage:link
    ```

### Running the Application

You will need two terminal tabs open:

1. Tab 1: Vite Dev Server

    ```bash
    npm run dev
    ```

2. Tab 2: Laravel Local Server

    ```bash
    php artisan serve
    ```

Visit http://127.0.0.1:8000 in your browser. You will be redirected to the Login page.

### Testing the Map

1. **Login**: Register a new account or use a seeded user if available.
2. **View Markers**: Upon login, the map centers on Yangon. You should see custom icons for Restaurants, Hotels, and Cafes.
3. **Filtering**: Pan the map away from the center. Notice the sidebar list updates dynamically via Inertia partial reloads.
4. **Detail View**: Click a marker or a list item to see the business details, including a scrollable photo gallery and dynamic star ratings.
5. **Responsive**: Use Chrome DevTools to toggle mobile view; the sidebar will transform into a bottom sheet.

### Project Structure (Key Files)
- ```app/Http/Controllers/MapController.php```: Handles spatial query logic.
- ```app/Http/Resources/CustomerResource.php```: Transforms model data for the frontend.
- ```resources/js/Pages/Maps/MapView.vue```: The main UI.
- ```resources/js/Components/Map/LeafletEngine.vue```: Isolated Leaflet API logic.
- ```resources/js/Components/Map/MapLibreEngine.vue```: Isolated Map Libre API logic.
- ```resources/js/Components/Map/OpenLayersEngine.vue```: Isolated Open Layers API logic.
- ```database/seeders/CustomerSeeder.php```: Contains the test.
