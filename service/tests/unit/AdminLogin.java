package FlexscoreUnitTests;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.firefox.FirefoxDriver;

public class AdminLogin {

	public static void main(String[] args) throws InterruptedException {

            WebDriver driver= new FirefoxDriver();
            driver.get("http://localhost/dev/");
            driver.manage().window().maximize();
            driver.findElement(By.id("signinPopupButton")).click();
	    driver.findElement(By.id("username")).clear();
	    driver.findElement(By.id("username")).sendKeys("ranjan.kumarrajeev@gmail.com");
	    driver.findElement(By.id("password")).clear();
	    driver.findElement(By.id("password")).sendKeys("ranjan123");
	    driver.findElement(By.id("loginButton")).click();
	    Thread.sleep(5000);
	    driver.findElement(By.id("sendEmailVerification")).click();
	    Thread.sleep(5000);
	    driver.findElement(By.id("iAmDone")).click();
	    Thread.sleep(5000);
	    driver.findElement(By.id("logout")).click();
	    driver.quit();
	}

}
