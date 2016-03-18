package Unit_Rajeev;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.firefox.FirefoxDriver;

public class advisorLogin {

	public static void main(String[] args) throws InterruptedException {

		WebDriver driver= new FirefoxDriver();
		driver.get("https://flextestuser:NS3CT4bL@dev.flexscore.com/test/");
		driver.findElement(By.id("signinPopupButton")).click();
		Thread.sleep(5000);
		driver.findElement(By.id("signupadvisortab")).click();
		Thread.sleep(5000);
	    driver.findElement(By.id("advusername")).clear();
	    driver.findElement(By.id("advusername")).sendKeys("advisor.fp@gmail.com");
	    driver.findElement(By.id("advpassword")).clear();
	    driver.findElement(By.id("advpassword")).sendKeys("truglobal");
	    driver.findElement(By.id("advLoginButton")).click();
	    Thread.sleep(5000);
	    driver.findElement(By.id("logout")).click();
	    driver.quit();
	    
	    
	}

}
