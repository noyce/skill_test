# AwesomeCorp Employee System Update

### SUBMISSION

### How to run
    docker compose up
    mailpit has been integrated with it for easy email testing in local environment

### 1. Update to PHP 8.4 from PHP 5.3 (updating to 7.1 made no sense as it's EOL long back)

Key changes:
- mysql_ functions have been replace with PDO
- All database queries are using prepared statements
- The singleton pattern in DatabaseProvider.php provides a centralized database connection

### 2. Adding Employee Gender Field

The Employee class already has support for metadata through the employee_meta_data table. The system uses a flexible approach to handle additional fields:

1. In The `Employee` class metadata support has been added with methods to save and load additional fields
3. The `EmployeeCreator` service centralizes the creation process.

This implementation allows for:
- Adding gender without modifying the exisiting database schema
- Easy addition of future fields through the same metadata system
- Separation of core and extended attributes

### 3. Government Reporting (CSV Report)

The system now generates a CSV report at `/tmp/employee_report.csv` with each new employee created. This is implemented in:

1. The `CsvAppender` service which appends employee data in the required format
2. The `EmployeeCreator` service calls this for both web and API-created employees
3. The format follows the requested `[employee id],[employee name],[employee email]` structure

## Future Improvements

### Code Structure and Architecture

1. **Implement Dependency Injection**: Replace manual service instantiation with a proper DI container
2. **Adopt MVC Pattern**: Separate controllers, models, and views more clearly
3. **Use Autoloading**: Replace manual includes with PSR-4 autoloading via Composer

### Security Enhancements

1. **Password Hashing**: Replace SHA-1 with PHP's password_hash() and password_verify()
2. **Input Validation**: Enhance validation for all user inputs
3. **CSRF Protection**: Add CSRF tokens to forms
4. **Output Escaping**: Use proper output escaping in templates

### Performance Optimizations

1. **Database Connection Pooling**: For handling higher loads
2. **Caching Layer**: Add caching for frequently accessed data
3. **Async Processing**: Move email sending and report generation to background jobs

### Testing

1. **Unit Tests**: Add unit tests for models and services
2. **Integration Tests**: Add tests for the web and API layers
3. **CI/CD Pipeline**: Implement automated testing and deployment

### User Experience

1. **Modern UI Framework**: Update the UI with a framework like Bootstrap or Tailwind
2. **Form Validation**: Add client-side validation
3. **User Feedback**: Improve error messages and notifications
