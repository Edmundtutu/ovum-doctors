<p align="center"><a href="https://yourapp.com" target="_blank"><img src="https://iamedmundtutuma.vercel.app/assets/images/ovumdoc.png" width="400" alt="Ovum-Doctor Logo"></a></p>

# Ovum-Doctor

## About Ovum-Doctor
Ovum-Doctor is a specialized web application designed for gynecologists to securely manage their patients efficiently. This application provides a user-friendly interface for tracking patient information, appointments, and medical history, ensuring that healthcare providers can focus on delivering quality care with top security and integrity.

### Key Features
- **Patient Management**: Easily add, update, and view patient records.
- **Appointment Scheduling**: Schedule and manage patient appointments with reminders.
- **Medical History Tracking**: Keep detailed records of patient medical history and treatments.
- **Secure Access**: Ensure patient data is protected with secure login using OTP and data encryption.
- **Reporting Tools**: Generate reports for patient visits, treatments, and outcomes.

## Installation & Setup

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/Edmundtutu/Ovum-Doctor.git
   cd ovum-doctor
   ```

2. **Install Dependencies**:
   ```bash
   composer install
   ```

3. **Set Up Environment**:
   ```bash
   cp .env.example .env
   ```
   Then edit the `.env` file with your database connection details:
   - DB_CONNECTION
   - DB_HOST
   - DB_PORT
   - DB_DATABASE
   - DB_USERNAME
   - DB_PASSWORD

4. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

5. **Run Database Migrations**:
   ```bash
   php artisan migrate
   ```

6. **Install JavaScript Dependencies**:
   ```bash
   npm install
   ```

7. **Compile Assets**:
   ```bash
   npm run dev
   ```

8. **Start the Application**:
   ```bash
   php artisan serve
   ```

Visit `http://localhost:8000` in your browser to access the application.

## Security
Ovum-Doctor takes patient data security seriously. The application implements:
- OTP-based secure login
- Data encryption
- Role-based access control
- Audit logging for all sensitive operations

## License
Ovum-Doctor is open-sourced software.