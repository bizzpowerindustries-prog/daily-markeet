package pk.dailymarkeet.seller

import android.app.Application
import android.app.NotificationChannel
import android.app.NotificationManager
import android.os.Build
import dagger.hilt.android.HiltAndroidApp

@HiltAndroidApp
class DailyMarkeetSellerApplication : Application() {
    
    override fun onCreate() {
        super.onCreate()
        createNotificationChannel()
    }
    
    private fun createNotificationChannel() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val channel = NotificationChannel(
                "dailymarkeet_seller_channel",
                "DailyMarkeet Seller Notifications",
                NotificationManager.IMPORTANCE_HIGH
            ).apply {
                description = "DailyMarkeet Seller order and earnings notifications"
                enableLights(true)
                enableVibration(true)
            }
            
            val manager = getSystemService(NotificationManager::class.java)
            manager.createNotificationChannel(channel)
        }
    }
}
