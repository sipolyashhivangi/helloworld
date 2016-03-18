package FlexUnittesting;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.firefox.FirefoxDriver;

public class MyAdvisorSearch {

	public static void main(String[] args) throws InterruptedException {

		WebDriver driver= new FirefoxDriver();
		driver.get("https://flextestuser:NS3CT4bL@dev.flexscore.com/test/ ");
		driver.findElement(By.id("signinPopupButton")).click();
	    driver.findElement(By.id("username")).clear();
	    driver.findElement(By.id("username")).sendKeys("user.advisor@gmail.com");
	    driver.findElement(By.id("password")).clear();
	    driver.findElement(By.id("password")).sendKeys("truglobal");
	    driver.findElement(By.id("loginButton")).click();
	    Thread.sleep(8000);
	    driver.findElement(By.xpath("//*[@id='gnav_user']/div/a/div[2]")).click();
	    driver.findElement(By.id("myadvisors")).click();
	    Thread.sleep(5000);
	    driver.findElement(By.xpath("//*[@id='myAdvisorContents']/div[3]/div[2]/button")).click();
	    Thread.sleep(5000);
	    driver.findElement(By.id("logout")).click();
	    driver.quit();
	}

	}


