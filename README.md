# AcademyPortal API

### Setup

1. Run `composer install` in root of project
2. Create database with name `academyPortal` and populate using latest version in db/
3. `php -S localhost:8080 -t public public/index.php`

### Routes
- for local development use localhost:8080/api/whatYouRequire as your URL

**/login**

POST
- Login registered user
- Sends:
	- `{'userEmail':'example@email.com', 'password':'password'}`
- Returns success true / false:
	- if registered user and correct email and password
		- `{'success':true, 'msg':'Valid User', 'data':[]}`  
	- if not registered or incorrect email and password
		- `{'success':false, 'msg':'Incorrect email or password.', 'data':[]}`


**/registerUser**

POST
- Registers a new user by saving in database
- Sends:
	- `{'userEmail':'example@email.com', 'password':'password'}`
- Checks if email already exists in database
- If email does not exist then saves to database
- Returns success true / false:
	- if new user registered successfully
		- `{'success':true, 'msg':'User registered', 'data':[]}`
	- if new user not registered successfully, either already exists or insert into database failed
		- `{'success':false, 'msg':'User not registered.', 'data':[]}`


**/applicationForm**

GET
- Gets available dropdown values from database for:
	- When would you like to join us? 
	- How did you hear about Mayden Academy? 
- Returns:
	- if GET request is successful
		- `{'success':true, 'msg':'Retrieved dropdown info.', 'data':['cohorts':'Available cohort values', 'hearAbout':'Available hear about values']}`


**/saveApplicant**

POST
- Saves a new application to the applicant table in the database
- Sends:
	- `{'name': 'example',
   	    'email': 'example@example.com',
 	    'phoneNumber': '0123456789',
	    'cohortId': 2,
	    'whyDev': 'example interest in development',
	    'codeExperience': 'example coding experience',
	    'hearAboutId': 3,
	    'eligible': '1' or '0',
	    'eighteenPlus': '1' or '0',
	    'finance': '1' or '0',
	    'notes': 'example notes'
	   }
- Returns success true / false:
	- if new applicant registered successfully
		- `{'success':true, 'msg':'Application Saved', 'data':[]}`
	- if new applicant not saved successfully
		- `{'success':false, 'msg':'Application Not Saved', 'data':[]}`

**/createHiringPartner**

POST
- Saves a new hiring partner to the hiring_partner_companies table in the database
- Sends:
	- `{'name': 'example',
   	    'companySize': '1',
 	    'techStack': 'example tech stack',
	    'postcode': 'BA1 1AA,
	    'phoneNumber': '01225 444444',
	    'companyURL': 'www.example.com',
	   }
- Returns success true / false:
	- if new applicant registered successfully
		- `{'success':true, 'msg':'Hiring Partner successfully added', 'data':[]}`
	- if new applicant not saved successfully
		- `{'success':false, 'msg':'Hiring Partner not added', 'data':[]}`