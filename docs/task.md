#1-Grid Technical PHP Assessment - 202109  

Attachment: JSON file: "challenge.json"   

##Assignment  

Write a process that will neatly transfers the contents of the JSON file to a database using PHP Laravel.  
You must design a simple UI which will allow the user to start, stop and cancel or resume the import process at any point.  
(Extra points for using Docker)  

##Conditions  

• [Primary] Create a process in such a way that it can be cut off at any moment (i.e. by a SIGTERM, power failure, etc.), and can easily be resumed from the point where you left off, (without duplicating data [1]).  
• Use a solid, but not exaggerated database model.  
Code for Eloquent models, relationships are not important here, we are more concerned about the data structure.  
• Only process records where the person is aged is between 18 and 65 (or unknown). • State your assumptions up front (readme doc is also appropriate)  
1 Please note that there is no guarantee that there are no duplicate records in the source file. There are no guaranteed unique (combinations of) properties.  

## What are we looking for
We mainly pay attention to the design and structure of the code. We would prefer a solid, neat, well- maintainable solution. We are particularly interested in the thinking behind your approach.  
Thought Experiment / Please include a short discussion around these topics in writing:  
• How would your solution differ if the source file suddenly becomes 500 times larger?  
• How much effort would it be to change your process to handle XML or CSV file types? (List your changes)  
• Suppose that only records of which the credit card number contains three of the same numbers consecutively should be processed, how would you approach that?  
  