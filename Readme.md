# Console Share

Console Share is a Laravel-based system that enables effortless sharing of your terminal over the web using simple `curl` and `script -q` commands. Whether you need to collaborate with team members or showcase your terminal activities, Console Share makes it quick and easy with just a single command.

## Features

- **Easy Terminal Sharing:** Share your terminal session with a single `curl` command.
- **Web-Based Access:** Access shared terminals through any web browser.
- **Secure Authorization:** Protect your sessions with bearer token authentication.
- **Real-Time Interaction:** Experience real-time terminal sharing using WebSockets.

## Getting Started

### Prerequisites

Ensure you have the following installed on your system:

- [PHP](https://www.php.net/) (version compatible with Laravel)
- [Composer](https://getcomposer.org/)
- [Laravel](https://laravel.com/)
- `curl`
- `script`

### Installation

1. **Clone the Repository:**


2. **Install Dependencies:**

   ```bash
   composer install
   ```

3. **Configure Environment Variables:**

   Duplicate the `.env.example` file and rename it to `.env`. Update the necessary environment variables, including `WEBSOCKET_SERVER_PORT`.

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

### Running the Application

Start the necessary servers using Laravel Artisan commands:

1. **Start the Laravel Development Server:**

   ```bash
   php artisan serve
   ```

2. **Start the WebSocket Server:**

   ```bash
   php artisan server:terminal-websocket debug
   ```

3. **Start the CURL Communication Server:**

   ```bash
   php artisan serve:curl-in debug
   ```

## Usage

### Sharing Your Terminal

To share your terminal session, execute the following command in your terminal:

```bash
curl --version > /dev/null && \
script --version > /dev/null && \
curl --http1.1 -s -N -H "Authorization: Bearer YOUR_ACCESS_TOKEN" https://www.terminalmirror.com/inputClient | \
script -q /dev/null | \
curl -H "Transfer-Encoding: chunked" -H "Authorization: Bearer YOUR_ACCESS_TOKEN" -X POST -T - https://www.terminalmirror.com/outputClient
```

**Replace `YOUR_ACCESS_TOKEN`** with your actual bearer token.

## Operation Principle

Console Share operates using three HTTP servers to facilitate terminal sharing:

1. **Laravel Development Server (`php artisan serve`):**
   
   - Serves the Laravel application.

2. **WebSocket Server (`php artisan server:terminal-websocket debug`):**
   
   - Manages real-time communication between clients and the server.
   - **Flow:** `[Web Browser Client]` ↔ `[WebSocket Server]`

3. **CURL Communication Server (`php artisan serve:curl-in debug`):**
   
   - Handles the input and output of terminal data via `curl`.
   - **Flow:** `[inputClient curl]` → `script -q` → `[outputClient curl]`

### CURL Command Workflow

The core operation involves reading input from a custom HTTP socket AMP server, piping it through the `script -q` command, and writing the output to another custom HTTP socket AMP server.

```bash
[inputClient curl] | script -q | [outputClient curl]
```

**Detailed Steps:**

1. **Read Input:**
   
   ```bash
   curl --http1.1 -s -N -H "Authorization: Bearer YOUR_ACCESS_TOKEN" http://localhost:3005/inputClient
   ```

2. **Pipe Through `script -q`:**
   
   ```bash
   | script -q /dev/null
   ```

3. **Write Output:**
   
   ```bash
   | curl -H "Transfer-Encoding: chunked" -H "Authorization: Bearer YOUR_ACCESS_TOKEN" -X POST -T - http://localhost:3005/outputClient
   ```

### Example Command

```bash
curl --http1.1 -s -N -H "Authorization: Bearer 2283ca20ac84d62bf52819474a1d5f00" http://localhost:3005/inputClient | script -q | curl -H "Transfer-Encoding: chunked" -H "Authorization: Bearer 2283ca20ac84d62bf52819474a1d5f00" -X POST -T - http://localhost:3005/outputClient
```

## Testing

To test Console Share, follow these steps:

1. **Configure WebSocket Server Port:**
   
   Update the `.env` file with your desired `WEBSOCKET_SERVER_PORT`.

2. **Start the WebSocket Server:**

   ```bash
   php artisan server:terminal-websocket debug
   ```

3. **Create a New Session:**

   Navigate to:

   ```
   http://localhost:8000/new-session
   ```

   Replace `8000` with your Laravel server port if different.

4. **Open Input Client Connection:**

   Use the session ID obtained from the previous step to open the WebSocket connection:

   ```
   ws://localhost:[WEBSOCKET_SERVER_PORT]/console-share?inputClient=[SESSION_ID_MD5]
   ```

## Debugging the AMPHP HTTP Server

For debugging purposes, you can use the following commands:

1. **Execute a Random Bash Command:**

   ```bash
   php artisan bash:random | bash | curl -H "Transfer-Encoding: chunked" -H "Content-Type: application/json" -X POST -T - http://localhost:3005/ -s
   ```

2. **Send Data to Console Share:**

   ```bash
   curl -H "Transfer-Encoding: chunked" -X POST -T - http://localhost:3005/console-share?inputClient=1675d32308cc13ddb8a14c6b5df0f59c -s
   ```

## License

This project is licensed under the MIT License.

---

Visit [terminalmirror.com](https://www.terminalmirror.com) for live test.
