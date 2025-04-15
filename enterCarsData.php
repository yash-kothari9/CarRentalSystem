<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "CarRentals";

// Turn on error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    echo "Connected successfully.<br>";

    // Insert new Model with multiple Cars
    $conn->query("INSERT INTO Models (Model, Make, VehicleType, VehicleRange)
                  VALUES ('X-Trail', 'Nissan', 'SUV', '700')");

    // Insert Cars with RentPerDay > 2000 and same model to test multiple cars per model
    $conn->query("INSERT INTO Cars (CarRegNo, Model, ManufacturingYear, RentPerDay, AvailabilityStatus)
                  VALUES 
                  ('TS10XX9999', 'X-Trail', 2023, 2500.00, 'Available'),
                  ('TS10YY9998', 'X-Trail', 2022, 2800.00, 'Rented')");

    // Insert Customers
    $conn->query("INSERT INTO Customers (SSN, Name, Gender, Age, MobileNumber, Email, City)
                  VALUES 
                  ('999887776655', 'Rohit Yadav', 'Male', 33, '9090909090', 'rohit@example.com', 'Warangal')");

    // Insert DrivingLicense for new Customer
    $conn->query("INSERT INTO DrivingLicenses (DLNumber, SSN)
                  VALUES ('DLR9998887', '999887776655')");

    // Insert Rental for expensive car
    $conn->query("INSERT INTO Rentals (RentalID, CarRegNo, CustomerSSN, RentalStartDate, RentalEndDate, BillAmount)
                  VALUES 
                  (201, 'TS10YY9998', '999887776655', '2025-04-05', '2025-04-07', 5600.00)");

    // Insert Maintenance within last 30 days
    $conn->query("INSERT INTO MaintenanceRecords (MaintenanceID, CarRegNo, MaintenanceDate, Description, Cost, NextMaintenanceDate)
                  VALUES 
                  (3001, 'TS10YY9998', '2025-03-30', 'Engine Tuning', 1200.00, '2025-06-30')");

    echo "Required test entries inserted successfully.";
    $conn->close();

} catch (mysqli_sql_exception $e) {
    die("Error: " . $e->getMessage());
}
?>