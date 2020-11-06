# taxi-api
Taxi API in Laravel, graduation work.

DB schema: https://ibb.co/kgGJS45

USER(ID,FirstName,Surname,Email,Password,UserType,ProfessionalQualifications,DrivingLicenceCategory,DrivingLicenceNumber)

VEHICLE(ID,LicencePlate,RegistrationDate,Brand,Model,ModelYear,DriverID,DateFrom,DateTo,Type,Color)

RIDE(ID,RequestTime,StartLocation,EndLocation,StartTime,EndTime,CustomerID,DispatcherID,DriverID)

USER_PHONE_NUMBER(ID,UserID,PhoneNumber)
