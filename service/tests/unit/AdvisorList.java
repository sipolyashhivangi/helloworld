package Unit_Rajeev;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.firefox.FirefoxDriver;

public class AdvisorList {

	public static void main(String[] args) throws InterruptedException {

		WebDriver driver= new FirefoxDriver();
		driver.get("https://flextestuser:NS3CT4bL@dev.flexscore.com/test/");
		driver.manage().window().maximize();
	    driver.findElement(By.id("signinPopupButton")).click();
	    driver.findElement(By.id("username")).clear();
	    driver.findElement(By.id("username")).sendKeys("ranjan.kumarrajeev@gmail.com");
	    driver.findElement(By.id("password")).clear();
	    driver.findElement(By.id("password")).sendKeys("ranjan123");
	    driver.findElement(By.id("loginButton")).click();
	    Thread.sleep(8000);
	    driver.findElement(By.xpath("//*[@id='sendEmailVerification']")).click();
	    Thread.sleep(5000);
	    driver.findElement(By.xpath("//*[@id='iAmDone']")).click();
	    driver.findElement(By.xpath("//*[@id='gnav_user']/div/a/div[2]")).click();
	    Thread.sleep(5000);
	    driver.findElement(By.id("unassignedNotifyTags")).click();
	    Thread.sleep(5000);
	    driver.findElement(By.id("logout")).click();
	    driver.quit();
	}

}
