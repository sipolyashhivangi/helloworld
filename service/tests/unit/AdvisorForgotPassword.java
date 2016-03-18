package FlexUnittesting;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.firefox.FirefoxDriver;

public class AdvisorForgotPassword {

	public static void main(String[] args) throws InterruptedException {

		WebDriver driver=new FirefoxDriver();
		driver.get("https://flextestuser:NS3CT4bL@dev.flexscore.com/test/");
		driver.findElement(By.id("signinPopupButton")).click();
	    driver.findElement(By.id("signupadvisortab")).click();
	    driver.findElement(By.cssSelector("#forgotButtondiv > #forgotButton")).click();
	    driver.findElement(By.id("username")).clear();
	    driver.findElement(By.id("username")).sendKeys("advisor.fp@gmail.com");
	    driver.findElement(By.id("forgotButton")).click();
	    Thread.sleep(5000);
	    driver.findElement(By.id("username")).clear();
	    driver.findElement(By.id("username")).sendKeys("advisor.fp@gmail.com");
	    driver.findElement(By.id("forgotButton")).click();
		
	}

	}


