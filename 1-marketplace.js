function Cart(){
	//array to store all groups of items 
    this.itemGroups = [];
	
	//display tot num of items and cost
    this.showTotalAmount = function(){
        if (this.itemGroups.length == 0){
            document.write("<p> You have 0 item, for a total amount of 0$, in your cart! </p>");
        } else  {
           let total = this.getTotalAmount();
		   let totalWithTax = total * 1.15; 
           document.write(`<p>You have ${this.itemGroups.length} item groups, for a total of $${total.toFixed(2)}, or $${totalWithTax.toFixed(2)} with tax.</p>`);
        }
    }
	
	//add new group of items to cart
	this.addItemGroup = function(itemGroup){
		this.itemGroups.push(itemGroup);
	}
	//calculate tot amt
	this.getTotalAmount = function(){
		let totalAmount = 0;
		for(let i=0; i<this.itemGroups.length; i++){
			totalAmount += this.itemGroups[i].pricePerItem*this.itemGroups[i].numberOfItems;
		}
		
		return totalAmount;
	}
		
}
//represent one type of product w/ price and quantity
function ItemGroup(name, pricePerItem, numberOfItems){
		this.name = name; 
		this.pricePerItem = pricePerItem; 
		this.numberOfItems = numberOfItems;
}


document.write("<h2> 1) Creating the cart </h2>")
let my_cart = new Cart()
my_cart.showTotalAmount();

document.write("<h2> 2) Adding 15 pants at 10.05$ each to the cart! </h2>")
let pants = new ItemGroup("pants", 10.05, 15);
my_cart.addItemGroup(pants)
my_cart.showTotalAmount();

document.write("<h2> 3) Adding 1 coat at 99.99$ to the cart! </h2>")
let coat = new ItemGroup("pants", 99.99, 1);
my_cart.addItemGroup(coat)
my_cart.showTotalAmount();