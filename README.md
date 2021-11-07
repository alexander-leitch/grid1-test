# PHP Test
	

### Running code
	docker-compose up -d
	http://localhost:8000
	
	#If you want to connect to the DB
	mysql -usail -hlocalhost -P 3308 -p
	
	#Upload json files to
	app/storage/uploads

### Question
• How would your solution differ if the source file suddenly becomes 500 times larger?   
	Depending on the type of machine this is running on a file 500 times larger would first need to be broken up a bit to be able to run.  
	An easier solution to process these files would be to insert them into a MongoDB system and then process them once there.  

• How much effort would it be to change your process to handle XML or CSV file types? (List your changes)  
	I have marked out a section with TODO that would need to be updated depending on the type, a few variables also after that but simple todo.   

• Suppose that only records of which the credit card number contains three of the same numbers consecutively should be processed, how would you approach that?  
	I have marked out a location with a TODO to point out the area I would add a check on the card.