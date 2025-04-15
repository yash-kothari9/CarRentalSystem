<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "CarRentals";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    echo "<h2>Connected successfully to '$dbname'</h2>";

    function displayResults($result) {
        if ($result->num_rows > 0) {
            echo "<table border='1' cellpadding='5'><tr>";
            while ($fieldinfo = $result->fetch_field()) {
                echo "<th>{$fieldinfo->name}</th>";
            }
            echo "</tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $col) {
                    echo "<td>$col</td>";
                }
                echo "</tr>";
            }
            echo "</table><br>";
        } else {
            echo "<p><i>No results found.</i></p>";
        }
    }

    // ========================= VIEWS ==============================
    echo "<h3>Creating and Displaying Views</h3>";

    // View 1
    echo "<h4>View: AvailableCars</h4>";
    $conn->query("CREATE OR REPLACE VIEW AvailableCars AS
        SELECT Ca.CarRegNo, M.Make, Ca.Model, Ca.RentPerDay, Ca.ManufacturingYear
        FROM Cars Ca
        JOIN Models M ON Ca.Model = M.Model
        WHERE Ca.AvailabilityStatus = 'Available'");
    $result = $conn->query("SELECT * FROM AvailableCars");
    displayResults($result);

    // View 2
    echo "<h4>View: RentalHistory</h4>";
    $conn->query("CREATE OR REPLACE VIEW RentalHistory AS
        SELECT R.RentalID, C.Name AS CustomerName, Ca.CarRegNo, M.Make, R.RentalStartDate, R.RentalEndDate, R.BillAmount
        FROM Rentals R
        JOIN Customers C ON R.CustomerSSN = C.SSN
        JOIN Cars Ca ON R.CarRegNo = Ca.CarRegNo
        JOIN Models M ON Ca.Model = M.Model");
    $result = $conn->query("SELECT * FROM RentalHistory");
    displayResults($result);

    // View 3
    echo "<h4>View: HighRentCars</h4>";
    $conn->query("CREATE OR REPLACE VIEW HighRentCars AS
        SELECT * FROM Cars WHERE RentPerDay > 2500");
    $result = $conn->query("SELECT * FROM HighRentCars");
    displayResults($result);

    // View 4
    echo "<h4>View: CityWiseRentals</h4>";
    $conn->query("CREATE OR REPLACE VIEW CityWiseRentals AS
        SELECT C.City, COUNT(*) AS Rentals
        FROM Rentals R
        JOIN Customers C ON R.CustomerSSN = C.SSN
        GROUP BY C.City");
    $result = $conn->query("SELECT * FROM CityWiseRentals");
    displayResults($result);

    // View 5
    echo "<h4>View: UpcomingMaintenance</h4>";
    $conn->query("CREATE OR REPLACE VIEW UpcomingMaintenance AS
        SELECT CarRegNo, MAX(NextMaintenanceDate) AS NextDue
        FROM MaintenanceRecords
        GROUP BY CarRegNo");
    $result = $conn->query("SELECT * FROM UpcomingMaintenance");
    displayResults($result);

    // ========================= QUERIES ============================
    echo "<hr><h2>Executing 20 Showcase Queries</h2>";

    $queries = [
        "1. Available Cars" =>
            "SELECT * FROM AvailableCars",

        "2. Cars with RentPerDay > 2000" =>
            "SELECT * FROM Cars WHERE RentPerDay > 2000",

        "3. Customers in Hyderabad" =>
            "SELECT * FROM Customers WHERE City = 'Hyderabad'",

        "4. Cars Manufactured After 2020" =>
            "SELECT * FROM Cars WHERE ManufacturingYear > 2020",

        "5. Rental History (Customer + Car Model)" =>
            "SELECT R.RentalID, C.Name, Ca.Model, R.RentalStartDate, R.RentalEndDate
             FROM Rentals R
             JOIN Customers C ON R.CustomerSSN = C.SSN
             JOIN Cars Ca ON R.CarRegNo = Ca.CarRegNo",

        "6. Model & Total Maintenance Cost" =>
            "SELECT Ca.Model, M.Make, SUM(MR.Cost) AS TotalMaintenanceCost
             FROM MaintenanceRecords MR
             JOIN Cars Ca ON MR.CarRegNo = Ca.CarRegNo
             JOIN Models M ON Ca.Model = M.Model
             GROUP BY Ca.Model, M.Make",

        "7. Customer and Driving License" =>
            "SELECT C.Name, DL.DLNumber 
             FROM Customers C
             JOIN DrivingLicenses DL ON C.SSN = DL.SSN",

        "8. Cars Never Rented" =>
            "SELECT * FROM Cars 
             WHERE CarRegNo NOT IN (SELECT CarRegNo FROM Rentals)",

        "9. Rentals Per City" =>
            "SELECT * FROM CityWiseRentals",

        "10. Average RentPerDay by VehicleType" =>
            "SELECT M.VehicleType, AVG(Ca.RentPerDay) AS AvgRent
             FROM Cars Ca
             JOIN Models M ON Ca.Model = M.Model
             GROUP BY M.VehicleType",

        "11. Car with Most Maintenance" =>
            "SELECT CarRegNo, COUNT(*) AS MaintenanceCount
             FROM MaintenanceRecords
             GROUP BY CarRegNo
             ORDER BY MaintenanceCount DESC
             LIMIT 1",

        "12. Customers with More Than 2 Rentals" =>
            "SELECT CustomerSSN, COUNT(*) AS RentalCount
             FROM Rentals
             GROUP BY CustomerSSN
             HAVING RentalCount > 2",

        "13. Customers Who Rented Expensive Cars" =>
            "SELECT DISTINCT C.*
             FROM Customers C
             WHERE C.SSN IN (
                 SELECT R.CustomerSSN
                 FROM Rentals R
                 JOIN Cars Ca ON R.CarRegNo = Ca.CarRegNo
                 WHERE Ca.RentPerDay > 2500
             )",

        "14. Customers Who Rented SUV Cars" =>
            "SELECT DISTINCT C.*
             FROM Customers C
             JOIN Rentals R ON C.SSN = R.CustomerSSN
             JOIN Cars Ca ON R.CarRegNo = Ca.CarRegNo
             JOIN Models M ON Ca.Model = M.Model
             WHERE M.VehicleType = 'SUV'",

        "15. Models With Multiple Cars" =>
            "SELECT Model, COUNT(*) AS CarCount
             FROM Cars
             GROUP BY Model
             HAVING CarCount > 1",

        "16. Car with Highest Rent" =>
            "SELECT * FROM Cars 
             WHERE RentPerDay = (SELECT MAX(RentPerDay) FROM Cars)",

        "17. Customers Who Rented and Have License" =>
            "SELECT C.Name
             FROM Customers C
             JOIN DrivingLicenses DL ON C.SSN = DL.SSN
             WHERE C.SSN IN (SELECT CustomerSSN FROM Rentals)",

        "18. Cars Maintained in Last 30 Days" =>
            "SELECT * FROM MaintenanceRecords 
             WHERE MaintenanceDate >= CURDATE() - INTERVAL 30 DAY",

        "19. Last Maintenance Description per Car" =>
            "SELECT CarRegNo, Description, MaintenanceDate
             FROM MaintenanceRecords MR
             WHERE (CarRegNo, MaintenanceDate) IN (
                 SELECT CarRegNo, MAX(MaintenanceDate)
                 FROM MaintenanceRecords
                 GROUP BY CarRegNo
             )",

        "20. Total Income by Car" =>
            "SELECT CarRegNo, SUM(BillAmount) AS TotalIncome
             FROM Rentals
             GROUP BY CarRegNo"
    ];

    foreach ($queries as $title => $sql) {
        echo "<hr><h3>$title</h3>";
        $result = $conn->query($sql);
        displayResults($result);
    }

    $conn->close();

} catch (mysqli_sql_exception $e) {
    die("<strong>Error:</strong> " . $e->getMessage());
}
?>