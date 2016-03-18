package FlexScoreUnit;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.testng.annotations.Test;

public class AssignAdvisor {
	
@Test(description="Assign Advisor")
	
	public void AssignAdvisor1() throws InterruptedException{
		WebDriver driver= new FirefoxDriver();
		driver.get("https://flextestuser:NS3CT4bL@dev.flexscore.com/test/");
		driver.manage().window().maximize();
	    driver.findElement(By.id("signinPopupButton")).click();
		Thread.sleep(8000);
	    driver.findElement(By.id("username")).clear();
	    driver.findElement(By.id("username")).sendKeys("dev1@test.com");
	    driver.findElement(By.id("password")).clear();
	    driver.findElement(By.id("password")).sendKeys("testing1");
	    driver.findElement(By.id("loginButton")).click();
	    Thread.sleep(8000);
	    driver.findElement(By.id("signoutAndClose")).click();
	    driver.findElement(By.cssSelector("div.gnavName")).click();
	    driver.findElement(By.linkText("Advisor List")).click();
	    Thread.sleep(8000);
	    driver.findElement(By.xpath("//*[@id='unassigned']")).click();
	    Thread.sleep(5000);
	    driver.findElement(By.xpath("//*[@id='allAdvisors']/div[3]/ul/li[2]/div/ul/li[8]/a[6]")).click();
	    driver.findElement(By.xpath("//*[@id='logout']")).click();
	    driver.quit();
}
}
