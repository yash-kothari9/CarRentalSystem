<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "CarRentals";

// Turn on error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Connect to the database
    $conn = new mysqli($servername, $username, $password, $dbname);
    echo "Connected successfully to '$dbname'<br>";

    // Create Models table
    $conn->query("CREATE TABLE IF NOT EXISTS Models (
        Model VARCHAR(50) PRIMARY KEY,
        Make VARCHAR(50),
        VehicleType VARCHAR(50),
        VehicleRange VARCHAR(50)
    )");
    echo "Table 'Models' created successfully.<br>";

    // Create Cars table
    $conn->query("CREATE TABLE IF NOT EXISTS Cars (
        CarRegNo VARCHAR(20) PRIMARY KEY,
        Model VARCHAR(50),
        ManufacturingYear INT,
        RentPerDay DECIMAL(10,2),
        AvailabilityStatus VARCHAR(20),
        FOREIGN KEY (Model) REFERENCES Models(Model)
    )");
    echo "Table 'Cars' created successfully.<br>";

    // Create Customers table
    $conn->query("CREATE TABLE IF NOT EXISTS Customers (
        SSN VARCHAR(15) PRIMARY KEY,
        Name VARCHAR(100),
        Gender VARCHAR(10),
        Age INT,
        MobileNumber VARCHAR(15),
        Email VARCHAR(100),
        City VARCHAR(50)
    )");
    echo "Table 'Customers' created successfully.<br>";

    // Create DrivingLicenses table
    $conn->query("CREATE TABLE IF NOT EXISTS DrivingLicenses (
        DLNumber VARCHAR(20) PRIMARY KEY,
        SSN VARCHAR(15) UNIQUE,
        FOREIGN KEY (SSN) REFERENCES Customers(SSN)
    )");
    echo "Table 'DrivingLicenses' created successfully.<br>";

    // Create Rentals table
    $conn->query("CREATE TABLE IF NOT EXISTS Rentals (
        RentalID INT PRIMARY KEY,
        CarRegNo VARCHAR(20),
        CustomerSSN VARCHAR(15),
        RentalStartDate DATE,
        RentalEndDate DATE,
        BillAmount DECIMAL(10,2),
        FOREIGN KEY (CarRegNo) REFERENCES Cars(CarRegNo),
        FOREIGN KEY (CustomerSSN) REFERENCES Customers(SSN)
    )");
    echo "Table 'Rentals' created successfully.<br>";

    // Create MaintenanceRecords table
    $conn->query("CREATE TABLE IF NOT EXISTS MaintenanceRecords (
        MaintenanceID INT PRIMARY KEY,
        CarRegNo VARCHAR(20),
        MaintenanceDate DATE,
        Description TEXT,
        Cost DECIMAL(10,2),
        NextMaintenanceDate DATE,
        FOREIGN KEY (CarRegNo) REFERENCES Cars(CarRegNo)
    )");
    echo "Table 'MaintenanceRecords' created successfully.<br>";

    $conn->close();

} catch (mysqli_sql_exception $e) {
    die("Error: " . $e->getMessage());
}
?>