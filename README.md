# **Laravel News Aggregation API**

This repository hosts a backend API built with Laravel 10/11 and PHP 8.3, designed for a modern news aggregation service. It provides endpoints for retrieving articles, filtering by common criteria (date, category, source), and, crucially, tailoring content delivery based on **authenticated user preferences**.

The project is fully containerized using Docker and utilizes a PostgreSQL database.

## **🚀 Getting Started**

These instructions will get a copy of the project up and running on your local machine.

### **Prerequisites**

You must have the following software installed on your system:

* **Docker Desktop** (or Docker Engine and Docker Compose)

### **Setup and Installation**

Follow these steps to initialize and run the application using Docker Compose:

1. **Clone the Repository:**  
   git clone \[your-repo-link\]  
   cd laravel-project

2. Environment Setup:  
   Create a .env file in the root directory. You can start by copying the example file:  
   cp .env.example .env

   Ensure your .env file contains the following database settings, which match the docker-compose.yml file:  
   \# Application URL  
   APP\_URL=http://localhost:5555

   \# Database Configuration (PostgreSQL defined in docker-compose.yml)  
   DB\_CONNECTION=pgsql  
   DB\_HOST=db  
   DB\_PORT=5432  
   DB\_DATABASE=laravel  
   DB\_USERNAME=laravel  
   DB\_PASSWORD=secret
   NEWSAPI\_KEY="your_key"
   GUARDIAN\_KEY="test"
   NYT\_KEY="your_key"



   *Note: You may need to generate an APP\_KEY if it's missing in your .env.*  
3. Build and Run Containers:  
   Execute the following command to build the PHP application container and start the PostgreSQL database container.  
   docker-compose up \--build \-d

   *The API will be available at http://localhost:5555.*  
4. Database Migration and Seeding:  
   Once the containers are running, you need to execute the database migrations and seed the initial data (e.g., sources, categories, and dummy articles).  
   Run the following command to execute operations inside the app container:  
   docker-compose exec app php artisan migrate \--seed

## **⚙️ Technical Features and Data Aggregation**

This section highlights the architecture used for data ingestion and delivery:

### **Content Providers**

The API aggregates articles from three major external providers, demonstrating robustness and integration flexibility:

1. **The Guardian**  
2. **NewsAPI**  
3. **The New York Times**

### **Dynamic Data Management**

**Sources**, **Categories**, and **Authors** are dynamically managed:

* **Categories:** Due to the wide and potentially endless variation of categories returned by the external providers, category records are dynamically created and stored upon article ingestion, ensuring the API's filtering options stay comprehensive and up-to-date with the ingested data.  
* **Authors:** Similar to categories, author data is also dynamically processed upon ingestion due to variations in provider formatting and alias usage, guaranteeing a complete and accurate list of authors for filtering purposes.

### **Article Duplication Prevention (Checksum)**

To maintain data integrity and prevent redundant storage, a **checksum mechanism** is implemented during the article ingestion process. This ensures that even if the same article is returned by multiple providers or during multiple ingestion runs, it is only stored once in the database.

### **Standardized API Response (Custom Trait)**

To ensure consistency and efficiency, a custom **API Response Trait** is utilized across all API endpoints. This trait standardizes the JSON response structure for both single resources and collections, and it automatically integrates **pagination metadata** for endpoints returning lists (such as /articles). This ensures predictable and performant data delivery.

## **📋 API Endpoints Reference**

The API is structured into three main groups: Public Access, Authentication, and Authenticated Access (including User Preferences).

The base URL for all endpoints is http://localhost:5555/api.

### **1\. Public Access (No Authentication Required)**

| Method | Endpoint | Description |
| :---- | :---- | :---- |
| GET | /sources | Retrieve a list of all available news sources. |
| GET | /sources/{key} | Retrieve a specific source by its unique key. |
| GET | /categories | Retrieve a list of all article categories. |
| GET | /categories/{slug} | Retrieve a specific category by its slug. |
| GET | /articles | Retrieve a paginated list of articles. See the **Filtering & Sorting** section below for available query parameters. |
| GET | /articles/{id} | Retrieve a single article by its ID. |

#### **Query Parameters for GET /articles (Filtering & Sorting)**

The primary article endpoint supports comprehensive filtering and sorting using the following query parameters:

| Parameter | Type | Example | Description |
| :---- | :---- | :---- | :---- |
| **q** | string | q=tesla | Full-text search term, matching against the article's title or summary (case-insensitive). |
| **source** | string | source=cnn,bbc | Filter by one or more news sources. Use comma-separated source keys. |
| **category** | string | category=sports,tech | Filter by one or more categories. Use comma-separated category slugs. |
| **author** | string | author=john%20smith | Filter by one or more authors. Use comma-separated author names. |
| **from** | date | from=2023-01-01 | Filter articles published **on or after** this date. (Field: published\_at). |
| **to** | date | to=2023-12-31 | Filter articles published **on or before** this date. (Field: published\_at). |
| **lang** | string | lang=en,es | Filter by one or more article languages. Use comma-separated language codes. |
| **sort\_by** | string | sort\_by=title | Defines the field to sort by. **Allowed values:** published\_at (default), title, created\_at. |
| **sort\_order** | string | sort\_order=asc | Defines the sort direction. **Allowed values:** desc (default), asc. |

### **2\. Authentication**

| Method | Endpoint | Description |
| :---- | :---- | :---- |
| POST | /register | Create a new user account. Returns a JWT/Sanctum token upon success. |
| POST | /login | Authenticate a user. Returns a JWT/Sanctum token upon success. |

### **3\. Authenticated Access (auth:sanctum Middleware Required)**

These routes require a valid Sanctum token passed in the Authorization: Bearer \<token\> header.

| Method | Endpoint | Description |  |
| :---- | :---- | :---- | :---- |
| GET | /user | Retrieve details of the currently authenticated user. |  |
| POST | /logout | Invalidate the current user's session token. |  |

#### **User Preferences (Core Feature)**

This module allows users to define their specific news preferences, demonstrating a key feature for personalized content delivery.

| Method | Endpoint | Description |
| :---- | :---- | :---- |
| GET | /user/preferences | Retrieve the authenticated user's saved preferences (e.g., selected sources, categories, authors). |
| **PUT** | /user/preferences | **Update** the authenticated user's preferences. |

#### **The Role of User Preferences in Article Retrieval**

When a logged-in user hits the public /articles endpoint, the frontend application **must first authenticate** to retrieve the user's token.

While the /articles route itself is public, in a complete system, the backend would typically check the existence of an Authorization header to determine if it should apply the stored user preferences *on top of* any provided query parameters. This ensures personalized results for signed-in users.

## **🌟 Future Enhancements (Roadmap)**

To further improve performance, scalability, and usability, the following enhancements are suggested:

### **Caching Strategy**

Implement a robust caching layer (e.g., using Redis) for high-traffic endpoints (like /articles and /sources). Caching would be applied to:

* Frequently accessed public article results.  
* User preferences data for faster lookups.

### **API Documentation**

Integrate an API documentation tool, such as **Swagger (OpenAPI)**, to provide interactive, up-to-date documentation for all endpoints. This would greatly assist frontend developers in consuming the API and serve as comprehensive project documentation.
