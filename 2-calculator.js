function compute_days(){
    
	const dobString = get_dob();
	const dob = new Date(dobString);
	const today = new Date();
    
	
	if(isNaN(dob.getTime())){
		write_answer_days("invalid date format"); 
		return;
	}
	
    //computing difference in milliseconds
	const diffMs = today - dob; 
	
	//convert to days
	const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
	
   

    //displaying result
    write_answer_days(`You're approximately ${diffDays} days old`);
}



function compute_circle(){
    const screen = get_screen_dims();
    
	//largest circle fits in the smallest screen dimension
	const diameter = Math.min(screen.width, screen.height); 
	const radius = diameter / 2; 
	
	//get area
	const area = Math.PI * Math.pow(radius, 2); 

	//display result
    write_answer_circle(`Largest circle radius: ${radius.toFixed(2)}px<br>Area: ${area.toFixed(2)} square px`);  
}

//checking palindromes
function check_palindrome(){
	//get text input
    const text_input = get_palindrome();
    
	//convert to lowercase and remove all chars that aren't letters/numbers
    const cleaned = text_input.toLowerCase().replace(/[^a-z0-9]/g, '');
	
	let isPalindrome = true; 
	
	//compare chars from start to end
	for(let i=0; i < cleaned.length/2; i++){
		if(cleaned[i] !== cleaned[cleaned.length - 1 -i]){
			isPalindrome = false; 
			break;
		}
	}
	
	if(isPalindrome){
		write_answer_palindrome(`"${text_input}" is a palindrome!`); 
	}
	else{
		write_answer_palindrome(`"${text_input}" is NOT a palindrome.`); 
	}
}
	
    
    



function create_fibo(){    

	//get user input
    const fibo_length = get_fibo_length();
	
	
	//validate input
	if(fibo_length <= 0){
		write_answer_fibo("Please enter a positive number greater than 0."); 
		return;
	}
	
	const fibo = [0, 1]; 
	
	//compute sequence
	for(let i=2; i < fibo_length; i++){
		fibo.push(fibo[i - 1] + fibo[i - 2]); 
	}
	
	let result; 
	if(fibo_length ===1){
		result = [0];
	}
	else{
		result = fibo.slice(0, fibo_length); 
	}
	
	
   //display
    write_answer_fibo(`Fibonacci sequence of length ${fibo_length}: <br>${result.join(', ')}`);
}

function get_fibo_length(){
	//get number in input field and convert it to integer
	const length = parseInt(document.getElementById("fibo_length").value);
	return length; 
}