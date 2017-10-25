-- Q: Пост охраны - связан с объектом или объектами?

use erpbase;
-- ===================================================================================
--
-- Tickets server database
--
-- 170607, Intep Ltd.
--
-- ===================================================================================

DROP TABLE IF EXISTS facility;
DROP TABLE IF EXISTS facilitytype;
DROP TABLE IF EXISTS street;
DROP TABLE IF EXISTS district;
DROP TABLE IF EXISTS locality;
DROP TABLE IF EXISTS localitytype;
DROP TABLE IF EXISTS region;
DROP TABLE IF EXISTS country;

DROP TABLE IF EXISTS division;
DROP TABLE IF EXISTS company;
DROP TABLE IF EXISTS companyrole;
DROP TABLE IF EXISTS companyform;
DROP TABLE IF EXISTS contractor;
DROP TABLE IF EXISTS contract;

DROP TABLE IF EXISTS payroll;
DROP TABLE IF EXISTS payrolloptype;
DROP TABLE IF EXISTS timesheetreport;
DROP TABLE IF EXISTS employeeinventorylog;
DROP TABLE IF EXISTS inventorytype;
DROP TABLE IF EXISTS vacation;
DROP TABLE IF EXISTS employee;
DROP TABLE IF EXISTS occupation;
DROP TABLE IF EXISTS employmenttype;
DROP TABLE IF EXISTS person;


-- ===================================================================================
-- Table: Address
-- ===================================================================================
CREATE TABLE country	-- Countries
    (
    id          INT NOT NULL AUTO_INCREMENT PRIMARY KEY,-- record ROWID
    countryname VARCHAR(80),  	-- country name
    countrymark VARCHAR(10),  	-- country sign
    countrycode INT  		-- country code
    );

CREATE TABLE region	-- Regions within the countries
    (
    id         INT NOT NULL AUTO_INCREMENT PRIMARY KEY,-- record ROWID
    regionname VARCHAR(80),  	-- region name
    regioncode INT,  		-- region code
    country_id INT NOT NULL,  	-- Country id

    FOREIGN KEY fk_country(country_id)
    REFERENCES country(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
    );

CREATE TABLE localitytype	-- Types of settlements, i.e. City / Town, etc.
    (
    id          INT NOT NULL AUTO_INCREMENT PRIMARY KEY,-- record ROWID
    localitytypename VARCHAR(80),  	-- locality type name
    localitytypecode INT  		-- locality type code
    );

CREATE TABLE locality	-- Settlements, i.e. Kharkiv, Kiyv
    (
    id          INT NOT NULL AUTO_INCREMENT PRIMARY KEY,-- record ROWID
    localityname VARCHAR(80),  	-- city name
    localitycode INT,  		-- city code
    region_id	INT NOT NULL,  	-- region id
    localitytype_id	INT NOT NULL,  	-- locality type id

    FOREIGN KEY fk_region(region_id)
    REFERENCES region(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

    FOREIGN KEY fk_localitytype(localitytype_id)
    REFERENCES localitytype(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
    );

CREATE TABLE district	-- Districts within the settlements
    (
    id         INT NOT NULL AUTO_INCREMENT PRIMARY KEY,-- record ROWID
    districtname VARCHAR(80),  	-- district name
    districtcode INT,  		-- district code
    locality_id INT NOT NULL,  	-- locality id

    FOREIGN KEY fk_locality(locality_id)
    REFERENCES locality(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
    );

CREATE TABLE street	-- Streets
    (
    id          INT NOT NULL AUTO_INCREMENT PRIMARY KEY,-- record ROWID
    streetname VARCHAR(80),  	-- street name
    streettype CHAR(10),  	-- street type
    streetcode INT,  		-- street code
    locality_id	INT NOT NULL,  	-- locality id

    FOREIGN KEY fk_locality(locality_id)
    REFERENCES locality(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
    );

CREATE TABLE facilitytype	-- Facility types, i.e. structure, building, geopoint, etc
    (
    id          INT NOT NULL AUTO_INCREMENT PRIMARY KEY,-- record ROWID
    facilitycode 	CHAR(10),
    facilitytypename 	VARCHAR(100)
    );

CREATE TABLE facility	-- Facilities
    (
    id          INT NOT NULL AUTO_INCREMENT PRIMARY KEY,-- record ROWID
    facilityname VARCHAR(100), 	-- facility description
    facilityno  CHAR(10),  	-- building number
    facilityno1 CHAR(10),  	-- building alternative number
    storeysnum  INT,  		-- the number of storeys
    porchesnum  INT,  		-- the number of porches 
    latitude	DOUBLE,
    longitude	DOUBLE,
    street_id	INT,  		-- street id
    district_id	INT,  		-- district id
    facilitytype_id INT,		-- facility type id

    FOREIGN KEY fk_street(street_id)
    REFERENCES street(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

    FOREIGN KEY fk_district(district_id)
    REFERENCES district(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

    FOREIGN KEY fk_facilitytype(facilitytype_id)
    REFERENCES facilitytype(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
    );
-- ===================================================================================
-- Table: Contract 
-- ===================================================================================
CREATE TABLE contract	-- Contracts
    (
    id          	INT NOT NULL AUTO_INCREMENT PRIMARY KEY, -- record ROWID
    contractno	 	CHAR(10),       -- Contract number
    contractno1	 	CHAR(10),       -- Contract number (additional)
    contractname 	VARCHAR(100),   -- Contract name
    contracsubject 	TEXT,		-- Contract subject
    contractdate 	DATE,		-- Contract date
    contractdays	INT,		-- Contract duration in days
    contractexpirationdate 	DATE,		-- Date after which contract will not be valid
    contractcompletiondate	DATE,		-- Date the contract should be completed
    contractcompleteddate	DATE,		-- Date of contract been completed
    contractwarrantydate	DATE,		-- Date the warranty will expire, if NULL, and warrantydays is null or 0, means no warranty 
    contractwarrantydays	INT,		-- Warranty period in days
    contracttotalamount		DECIMAL	-- Total contract value
     );

CREATE TABLE contractor	-- The contract's parties, individual  or legal person
    (
    id          	INT NOT NULL AUTO_INCREMENT PRIMARY KEY, -- record ROWID
    contractortype 	CHAR(10),       -- Contractor type(individual person/legal person)
    contractorname 	VARCHAR(100),   -- contractor name
    contractorcode	CHAR(10),	-- contractor registration number(INN/EDRPOU)
    contractorphone	CHAR(16),	-- contractor phone
    contractorfax	CHAR(16),	-- contractor fax
    contractoremail	CHAR(255),	-- contractor email
    contractorurl	CHAR(255),	-- contractor web url
    contractoraddress	VARCHAR(255),	-- contractor address
    signer_id		INT,		-- the contractor's person who signed the contract

    FOREIGN KEY fk_person(person_id)
    REFERENCES person(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
     );
-- ===================================================================================
-- Table: Company 
-- ===================================================================================
CREATE TABLE companyform	-- Form of company ownership, i.e.Ltd., OGSC
    (
    id          	INT NOT NULL AUTO_INCREMENT PRIMARY KEY, -- record ROWID
    companyform 	CHAR(20),     	-- Company form abbreviation
    companyformname 	VARCHAR(100),     	-- Company form full name
    companyformcode	INT	       	-- Company form code	
     );

CREATE TABLE companyrole	-- Company roles, i.e. customer, contractor, supplier
    (
    id          	INT NOT NULL AUTO_INCREMENT PRIMARY KEY, -- record ROWID
    companyrole 	CHAR(50),     	-- Company role
    companyrolecode	INT	       	-- Company role code	
     );

CREATE TABLE company	-- Companies 
    (
    id          	INT NOT NULL AUTO_INCREMENT PRIMARY KEY, -- record ROWID
    companyname 	VARCHAR(100),   -- Company name
    companycode		CHAR(10),	-- Company registration number
    companydate 	DATE,		-- Registration date
    companyphone	CHAR(16),	-- Company phone
    companyfax		CHAR(16),	-- Company phone
    companyemail	CHAR(255),	-- Company email
    companyurl		CHAR(255),	-- Company web url
    companyaddress	VARCHAR(255),	-- Company address

    companyform_id	INT NOT NULL,  	-- Company form id
    companyrole_id	INT NOT NULL,  	-- Company role id

    FOREIGN KEY fk_companyrole(companyrole_id)
    REFERENCES companyrole(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

    FOREIGN KEY fk_companyform(companyform_id)
    REFERENCES companyform(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
     );

CREATE TABLE division	-- Company divisions 
    (
    id        	   INT NOT NULL AUTO_INCREMENT PRIMARY KEY,     -- record ROWID
    divisionname VARCHAR(30),          	-- division name
    divisioncode CHAR(10),		-- division code
    company_id	INT NOT NULL,  		-- Company id

    FOREIGN KEY fk_company(company_id)
    REFERENCES company(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
     );
-- ===================================================================================
-- Table: Person
-- ===================================================================================
CREATE TABLE person	-- Private persons
    (
    id          	INT NOT NULL AUTO_INCREMENT PRIMARY KEY, -- record ROWID
    firstname		VARCHAR(50),	-- Person name[+patronymic]
    patronymic		VARCHAR(50),	-- Person patronymic
    lastname		VARCHAR(50),	-- Person surname
    personcode		CHAR(10),	-- Person code(INN)
    passportno		CHAR(16),	-- Person passport code
    passportdata	CHAR(16),	-- Person passport data
    personaddress	VARCHAR(255),	-- Person registration address
    currentaddress	VARCHAR(255),	-- Person current address
    postcode		INT,			-- Postal code
    personphone		CHAR(16),	-- Person phone
    personphone1	CHAR(16),	-- Person phone
    personemail		CHAR(255),	-- Person email
    personurl		CHAR(255),	-- Person web url
    sex			CHAR(10),	-- Person sex
    birthday		DATE,		-- Person date of birth
    married		BINARY(1) NOT NULL DEFAULT 0,
    education		VARCHAR(255)	-- Person education
     );

CREATE TABLE employmenttype	-- Types of employment, i.e. Full time, Part time, etc
    (
    id        	INT NOT NULL AUTO_INCREMENT PRIMARY KEY,     -- record ROWID
    employmenttype VARCHAR(40),         	-- employment type 
    employmentcode CHAR(10)		-- employment type code:
					-- FT - full time
					-- PT - part time
					-- UFT - full time unregistered
					-- UPT - part time unregistered
     );
CREATE TABLE occupation	-- Staffing table
    (
    id        	   INT NOT NULL AUTO_INCREMENT PRIMARY KEY,     -- record ROWID
    occupationname VARCHAR(30),          -- occupation name
    occupationcode CHAR(10)		-- occupation code
 );
 
CREATE TABLE occupationrate	-- Changelog for the hour rates for occupations 
    (
    id        	   INT NOT NULL AUTO_INCREMENT PRIMARY KEY,     -- record ROWID
    occupationratedate DATE,		-- date the occupation rate was modified
    occupationrate DECIMAL,		-- the occupation's hour wage-rate
    occupation_id	INT NOT NULL,  	-- occupation id

    FOREIGN KEY fk_occupation(occupation_id)
    REFERENCES occupation(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
     );

CREATE TABLE employee	-- Employees
    (
    id        	 	INT NOT NULL AUTO_INCREMENT PRIMARY KEY,     -- record ROWID
    employmentdate	TIMESTAMP,	-- employment date
    dismissaldate	TIMESTAMP,	-- dismissal date
    salary		DECIMAL,	-- Salary for month
    rate		DECIMAL,	-- Salary hour rate
    skillscategory	CHAR(10) ,	-- qualification category
    skillsrank		CHAR(10),	-- qualification rank
    certprofessional	DATE,		-- Skills certificate date 
    certmedical		DATE,		-- Medical certificate date 
    certnarcology	DATE,		-- Medical narcology certificate date 
    certpsych		DATE,		-- Medical psychology certificate date 
    certcriminal	DATE,		-- Certificate of absence of criminal record date
    statusmilitary	CHAR(10) ,	-- Is the Person liable for military call up
    statusdisability	INT,		-- Person disability group, 1...3, 0=no
    statuschernobyl	INT,		-- Person Chernobyl NPP status, 0=no,  1-4=category 1...4
    lastjob		VARCHAR(255),	-- Last place of work
    isfired		BINARY(1) NOT NULL DEFAULT 0,	-- not 0 if employee was fired
    person_id		INT NOT NULL,  	-- person id
    company_id		INT NOT NULL,  	-- company id
    occupation_id	INT NOT NULL,  	-- occupation id
    division_id	INT NOT NULL,  		-- division id
    employmenttype_id	INT NOT NULL,  	-- employment type id

    FOREIGN KEY fk_company(company_id)
    REFERENCES company(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

    FOREIGN KEY fk_person(person_id)
    REFERENCES person(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

    FOREIGN KEY fk_occupation(occupation_id)
    REFERENCES occupation(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

    FOREIGN KEY fk_division(division_id)
    REFERENCES division(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

    FOREIGN KEY fk_employmenttype(employmenttype_id)
    REFERENCES employmenttype(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
     );

CREATE TABLE vacation	-- Employee leave records
    (
    id        	 	INT NOT NULL AUTO_INCREMENT PRIMARY KEY,     -- record ROWID
    employee_id		INT NOT NULL,  	-- employee id
    vacationfrom	TIMESTAMP,	-- vacation start date (planned if in a future, actual otherwise)
    vacationto		TIMESTAMP,	-- vacation end date (planned if in a future, actual otherwise)
    vacationtotaldays	INT,		-- total duration of vacations in current year

    FOREIGN KEY fk_employee(employee_id)
    REFERENCES employee(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
     );

CREATE TABLE employeeeventtype	-- Employee leave records
    (
    id        	 	INT NOT NULL AUTO_INCREMENT PRIMARY KEY,     -- record ROWID
    employeeeventcode	CHAR(10),	-- the event code
    employeeeventname	VARCHAR(255)	-- name of event
     );

CREATE TABLE employeeevent
    (
    id        	 	INT NOT NULL AUTO_INCREMENT PRIMARY KEY,     -- record ROWID
    eeventdate		TIMESTAMP,	-- vacation start date (planned if in a future, actual otherwise)
    eeventdescription	VARCHAR(255),	-- description of event
    eeventtype_id	INT,	-- eventtype id
    employee_id		INT NOT NULL,  	-- employee id

    FOREIGN KEY fk_employee(employee_id)
    REFERENCES employee(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

    FOREIGN KEY fk_employeeeventtype(eeventtype_id)
    REFERENCES employeeeventtype(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
     );

CREATE TABLE inventorytype
    (
    id        	 	INT NOT NULL AUTO_INCREMENT PRIMARY KEY,     -- record ROWID
    inventorycode	CHAR(10),	-- the inventory code
    inventoryname	VARCHAR(255),	-- name of the inventory goods
    inventorydays	INTEGER	-- Inventory usage term in days
     );

CREATE TABLE employeeinventorylog
    (
    id        	 	INT NOT NULL AUTO_INCREMENT PRIMARY KEY,     -- record ROWID
    inventorydate	TIMESTAMP,	-- the date the inventory were given
    eventdescription	VARCHAR(255),	-- description the reason for moving goods
    inventoryvalue	DECIMAL,    	-- the value of inventory
    employee_id		INT NOT NULL,  	-- employee id
    inventorytype_id	INT NOT NULL,  	-- inventory id

    FOREIGN KEY fk_employee(employee_id)
    REFERENCES employee(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

    FOREIGN KEY fk_inventorytype(inventorytype_id)
    REFERENCES inventorytype(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
     );

CREATE TABLE timesheetreport
    (
    id        	 	INT NOT NULL AUTO_INCREMENT PRIMARY KEY,     -- record ROWID
    employee_id		INT NOT NULL,  	-- employee id
    -- guardpost_id 	INT NOT NULL, -- guard post with which the record linked, see below
    contract_id		INT NOT NULL,  	-- contract id
    timesheetdate	TIMESTAMP,	-- record date
    timesheethours	INT,		-- record time

    FOREIGN KEY fk_employee(employee_id)
    REFERENCES employee(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

    FOREIGN KEY fk_contract(contract_id)
    REFERENCES contract(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
     );

CREATE TABLE payrolloptype
    (
    id        	 	INT NOT NULL AUTO_INCREMENT PRIMARY KEY,     -- record ROWID
    optypename VARCHAR(40),
    optypetype VARCHAR(10),	-- DEBET or CREDIT
    optypecode CHAR(10)		-- CS - Salary payment, CREDIT
							-- DS - Payroll, DEBET
							-- CB - Bonus payment, CREDIT
							-- DB - Charging bonus, DEBET
							-- CL - Issuing loans, CREDIT
							-- DL - Repaying loans, DEBET
							-- CI - Issuing inventory, CREDIT
							-- DI - Refund money for inventory, DEBET
							-- CP - Charging penalties, CREDIT
							-- DP - payment the fine, DEBET
     );

CREATE TABLE payroll		-- Payroll log
    (
    id        	 	INT NOT NULL AUTO_INCREMENT PRIMARY KEY,     -- record ROWID
    author_id		INT NOT NULL,  	-- employee id
    employee_id		INT NOT NULL,  	-- employee id
    opcode_id		INT NOT NULL,  	-- payrolloptype id
    opdate			DATE,		-- operation date
    payrollperiod	DATE,		-- operation period
    opcausedate		TIMESTAMP,	-- 
    paidhours		INT,		--
    paidrate		DECIMAL,	--
    opdebet			DECIMAL,	-- operation debet amount
    opcredit		DECIMAL,	-- operation credit amount
    opdescription	VARCHAR(255),	-- operation notes
    opdescription1	VARCHAR(255),	-- operation notes
    opdescription2	VARCHAR(255),	-- operation notes
    opdescription3	VARCHAR(255),	-- operation notes
    payrolloptype_id	INT NOT NULL,

    FOREIGN KEY fk_employee(employee_id)
    REFERENCES employee(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

    FOREIGN KEY fk_author(employee_id)
    REFERENCES employee(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

    FOREIGN KEY fk_payrolloptype(payrolloptype_id)
    REFERENCES payrolloptype(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
     );



-- ===================================================================================
-- SCADA database add-on
-- ===================================================================================


DROP TABLE IF EXISTS rtutype;
DROP TABLE IF EXISTS rtu;

CREATE TABLE rtutype
    (
    id          INT NOT NULL AUTO_INCREMENT PRIMARY KEY,     -- record ROWID
    rtutypename	CHAR(10),	-- RTU TYPE name
    rtutypecode INT		-- RTU TYPE code
     );

CREATE TABLE rtu
    (
     id         INT NOT NULL AUTO_INCREMENT PRIMARY KEY,     -- record ROWID
    rtuno	INT,		-- RTU communication number
    rtuserialno	VARCHAR(50),	-- RTU Serial Number
    rtuname	VARCHAR(100),	-- RTU description
    rtuphone	CHAR(20),	-- RTU communication phone address
    rtuip	CHAR(20),	-- RTU communication ip-address
    clocksyncperm BINARY(1),	-- Permission for RTU clock synchronization
    rtutype_id	INT NOT NULL,	-- RTU type id

    FOREIGN KEY fk_rtutype(rtutype_id)
    REFERENCES rtutype(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
     );

CREATE TABLE Elevator
    (
    id                  INT NOT NULL AUTO_INCREMENT PRIMARY KEY,     -- record ROWID
    elserialno		VARCHAR(50),	-- elevator Serial Number
    elinventoryno	VARCHAR(50),	-- elevator Inventory Number
    elporchno		INTEGER,	-- elevator porch  number
    eltype     		VARCHAR(10),    -- elevator type, position (left/right)
    elregyear     	VARCHAR(4),     -- Elevator Registration Year
    elload     		INTEGER,        -- Elevator carrying capacity
    elspeed        	NUMERIC(3,2),   -- speed limit
    facility_id		INT NOT NULL,	-- building id
    rtu_id		INT NOT NULL,	-- elevator RTU id

    FOREIGN KEY fk_facility(facility_id)
    REFERENCES facility(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

    FOREIGN KEY fk_rtu(rtu_id)
    REFERENCES rtu(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
     );



-- ===================================================================================
-- Tickets database add-on
-- ===================================================================================


DROP TABLE IF EXISTS ticket;

CREATE TABLE ticket
    (
    id        	 	INT NOT NULL AUTO_INCREMENT PRIMARY KEY,     -- record ROWID
    ticode		CHAR(50),	-- Ticket registration number
    tipriority		CHAR(20),	-- Ticket priority

    tiincidenttime	TIMESTAMP  NULL,	-- Date & time the incident for which ticket was created
    tiopenedtime	TIMESTAMP  NULL,	-- Date & time when ticket was inserted in db
    tiplannedtime	TIMESTAMP  NULL,	-- Planned date & time up to which the normal operation should be restored
    ticlosedtime	TIMESTAMP  NULL,	-- Date & time when the ticket was closed

    tiincidenttype	VARCHAR(200),	-- Ticket incident type (for example: intrusion to machinery room , emergency button of the lift cabin)
    tidescription	VARCHAR(255),	-- Ticket incident description

    tifacilitycode	CHAR(20),	-- code of facility where incident took place (for example the elevator's factory number)
    tifacilitycode1	CHAR(20),	-- code of facility where incident took place, additional (for example the elevator's inventory number)
    tiregion		CHAR(50),	-- region of facility where incident took place
    tiaddress		VARCHAR(255),	-- address of facility where incident took place

    tioriginator	VARCHAR(100),	-- name of the person who placed ticket into db

    ticaller		VARCHAR(100),	-- Trouble call caller name
    ticalleraddress	VARCHAR(200),	-- Trouble call caller address
    ticalltype		CHAR(50),	-- Trouble call type, (phone,emergency button,inner dispatcher line, etc)

    tiresumedtime	TIMESTAMP NULL	-- Date & time the resuming of normal operation
    );

    /********************************************************************************************************************************************************************

	Ticket status:

	-- INCOMING_UNREAD
	-- INCOMING_READ
	-- ACCEPTED
	-- ACCEPTED_ASSIGNED
	-- ACCEPTED_REASSIGNED
	-- ACCEPTED_DEFFERED
	-- ACCEPTED_RESUMED
	-- DISMISSED 
	-- DISMISSED_ACCEPTED
	-- COMPLETED
	-- COMPLETED_ACCEPTED

     	Ticket handling chain: 
		Head master:
     		Foreman:	INCOMING -> ACCEPTED_ASSIGNED -> [ACCEPTED_REASSIGNED ->] [ACCEPTED_DEFFERED ->] COMPLETED -> COMPLETED_ACCEPTED
     				INCOMING -> ACCEPTED_ASSIGNED -> [ACCEPTED_REASSIGNED ->] [ACCEPTED_DEFFERED ->] DISMISSED -> DISMISSED_ACCEPTED
     				INCOMING -> DISMISSED -> DISMISSED_ACCEPTED

     		Technician:	INCOMING -> ACCEPTED ->  [ACCEPTED_DEFFERED ->] COMPLETED -> COMPLETED_ACCEPTED
     				INCOMING -> ACCEPTED ->  [ACCEPTED_DEFFERED ->] DISMISSED -> DISMISSED_ACCEPTED
     				INCOMING -> DISMISSED -> DISMISSED_ACCEPTED

	Examples of usage (assuming ticket.id = 1):

    1. Dispatcher places the ticket into the system (after receiving the telemechanics system emergency message: The machinery door sensor alarms intrusion conditions):
	insert into ticket (
	ticode     , tiincidenttime  , tiopenedtime     , tiplannedtime   , ticlosedtime , tiincidenttype, tidescription         , tifacilitycode,tifacilitycode1,tiregion,    tiaddress                                 , tioriginator,ticaller, ticalleraddress,ticalltype) values(
	'170706-12','170706T12:55:00', '170706T13:10:00','170706T17:00:00', NULL         , 'дМП',          'Срабатывание датчика', '11110000'    ,'00001111'     ,"Московский","ул. Гв. Широнинцев, д.18, подъезд №3, лп", 'Иванов',    NULL    ,  NULL          ,'дМП');


    2. Dispatcher directly assigns ticket for defined foreman
	insert into ticketlog (
	tiltime          , tilstatus        ,tilsenderdesk  , tilreceiverdesk, tilsendername, tilreceivername,tiltext      , ticket_id) values(
	'170706T13:10:00', 'INCOMING','Диспетчерская', 'Участок 1'    , 'Иванов'     , 'Мастеровитый' ,'Поручи Васе', 1);

    3. Foreman reads the ticket:
	update ticketlog set tilreadflag=1 where tilreceivername like 'Мастеровитый%' and ticket_id = 1 and id = XXX;

    4. Foreman assigns ticket to technician:
	insert into ticketlog (
	tiltime          , tilstatus          ,tilsenderdesk  , tilreceiverdesk, tilsendername , tilreceivername,tiltext      , ticket_id) values(
	'170706T13:15:00', 'ACCEPTED_ASSIGNED','Участок 1'    , 'Участок 1'    , 'Мастеровитый', 'Бестолковый'  ,'Вася закрой дверь', 1);


    5. Technician reads the ticket:
	update ticketlog set tilreadflag = 1 where tilreceivername like 'Бестолковый%' and ticket_id = 1 and id = XXX;

    6. Technician starts trying to restore normal operation:
	insert into ticketlog (
	tiltime          , tilstatus , tilsenderdesk  , tilreceiverdesk, tilsendername , tilreceivername,tiltext      , ticket_id) values(
	'170706T13:25:15', 'ACCEPTED', 'Участок 1'    , 'Участок 1'    , 'Бестолковый', 'Мастеровитый'  ,'Went for work', 1);

    7. But this time there were force majeure circumstances, which leads the technician to defer the work :
	insert into ticketlog (
	tiltime          , tilstatus          , tilsenderdesk  , tilreceiverdesk, tilsendername , tilreceivername,tiltext      , ticket_id) values(
	'170706T14:25:10', 'ACCEPTED_DEFFERED', 'Участок 1'    , 'Участок 1'    , 'Бестолковый', 'Мастеровитый'  ,'I can't close the door, it has been stolen', 1);

    8. Foreman is forced to defer the ticket execution, do buying new door, then continues the work:
	insert into ticketlog (
	tiltime          , tilstatus            , tilsenderdesk  , tilreceiverdesk, tilsendername , tilreceivername,tiltext      , ticket_id) values(
	'170706T15:20:00', 'ACCEPTED_REASSIGNED', 'Участок 1'    , 'Участок 1'    , 'Мастеровитый', 'Бестолковый'  ,'Вася я купил дверь возьми и поставь', 1);


    9. Technician reads the ticket again:
	update ticketlog set tilreadflag = 1 where tilreceivername like 'Бестолковый%' and ticket_id = 1 and id = XXX;

    10. Technician starts trying to restore normal operation again:
	insert into ticketlog (
	tiltime          , tilstatus , tilsenderdesk  , tilreceiverdesk, tilsendername , tilreceivername,tiltext      , ticket_id) values(
	'170706T15:25:00', 'ACCEPTED_RESUMED', 'Участок 1'    , 'Участок 1'    , 'Бестолковый', 'Мастеровитый'  ,'Went for work', 1);

    11. Technician did all things well done:
	insert into ticketlog (
	tiltime          , tilstatus  , tilsenderdesk  , tilreceiverdesk, tilsendername , tilreceivername,tiltext      , ticket_id) values(
	'170706T16:25:00', 'COMPLETED', 'Участок 1'    , 'Участок 1'    , 'Бестолковый', 'Мастеровитый'  ,'Boss you owe me money', 1);


    12. Foreman tests if all wehe well done and reports to dispatcher the ticket is copmleted:
	insert into ticketlog (
	tiltime          , tilstatus  , tilsenderdesk  , tilreceiverdesk, tilsendername , tilreceivername, tiltext      , ticket_id) values(
	170706T16:30:10, 'COMPLETED_TESTED', 'Участок 1'    , 'Диспетчерская'  , 'Мастеровитый', 'Иванов'       , 'Заявка выполнена', 1);

    13. Dispatcher accepts the ticket completion:
	insert into ticketlog (
	tiltime          , tilstatus           , tilsenderdesk  , tilreceiverdesk, tilsendername, tilreceivername, tiltext  , ticket_id) values(
	'170706T16:40:10', 'COMPLETED_ACCEPTED', 'Диспетчерская', 'Участок 1'    , 'Иванов'     , 'Мастеровитый' , 'Принято', 1);

    14. Dispatcher closes the ticket:
	update ticket set ticlosedtime = '170706T16:40:10',tiresumedtime = '170706T16:40:10' where id=1;
     *********************************************************************************************************************************************************************/

CREATE TABLE ticketlog
    (
    id        	 	INT NOT NULL AUTO_INCREMENT PRIMARY KEY,     -- record ROWID

    tiltime		TIMESTAMP NULL,	-- Date & time of the ticket incoming event
    tilstatus		CHAR(50),	-- Status, see above status description for ticket
    tilreadflag		BINARY(1),	-- if 0 - it's unread message, otherwise message has been read

    tilsenderdesk	VARCHAR(200),	-- the devision who send the ticket
    tilreceiverdesk	VARCHAR(200),	-- the devision for whom the ticket sent
    tilsendername	VARCHAR(200),	-- the name of person who send the ticket
    tilreceivername	VARCHAR(200),	-- the name of person for whom the ticket sent
    tiltext		VARCHAR(255),	-- Inbound event description

    ticket_id		INT NOT NULL,	-- ticket id

    FOREIGN KEY fk_ticket(ticket_id)
    REFERENCES ticket(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
    );


-- ===================================================================================
-- Secuirity database add-on
-- ===================================================================================


DROP TABLE IF EXISTS guardpostrate;
DROP TABLE IF EXISTS guardpost;

CREATE TABLE guardpost	-- Security posts
    (
    id        	 	INT NOT NULL AUTO_INCREMENT PRIMARY KEY,     -- record ROWID
    guardpostcode	VARCHAR(10),
    guardpostname	VARCHAR(255),
    guardpost4watch	BINARY(1),		-- not NULL, not 0, means the remote guard post, so calculate the Prize when the double-watch was there
    facility_id 	INT NOT NULL,  	

    FOREIGN KEY fk_facility(facility_id)
    REFERENCES facility(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
     );

CREATE TABLE guardpostrate	-- Changelog for the hour wage-rates for security posts
    (
    id        	   INT NOT NULL AUTO_INCREMENT PRIMARY KEY,     -- record ROWID
    guardpostratedate DATE,		-- date the wage-rate was modified
    guardpostrate DECIMAL,		-- the occupation's hour wage-rate
    guardpost_id	INT NOT NULL,  	-- guardpost id

    FOREIGN KEY fk_guardpost(guardpost_id)
    REFERENCES guardpost(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
     );
     
CREATE TABLE guard2facility -- Table for binding of guard commanders to guarded objects, many2many
    (
    id        	 	INT NOT NULL AUTO_INCREMENT PRIMARY KEY,     -- record ROWID
    facility_id 	INT NOT NULL,  	
    employee_id 	INT NOT NULL,  	

    FOREIGN KEY fk_facility(facility_id)
    REFERENCES facility(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

    FOREIGN KEY fk_employee(employee_id)
    REFERENCES employee(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
     );
     
ALTER TABLE timesheetreport ADD COLUMN guardpost_id INT NOT NULL;
ALTER TABLE timesheetreport ADD CONSTRAINT fk_guardpost FOREIGN KEY (guardpost_id) REFERENCES guardpost(id) ON UPDATE CASCADE ON DELETE RESTRICT ;


