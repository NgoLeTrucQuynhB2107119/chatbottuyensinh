# This files contains your custom actions which can be used to run
# custom Python code.
#
# See this guide on how to implement these action:
# https://rasa.com/docs/rasa/custom-actions


# This is a simple example for a custom action which utters "Hello World!"

# from typing import Any, Text, Dict, List
#
# from rasa_sdk import Action, Tracker
# from rasa_sdk.executor import CollectingDispatcher
#
#
# class ActionHelloWorld(Action):
#
#     def name(self) -> Text:
#         return "action_hello_world"
#
#     def run(self, dispatcher: CollectingDispatcher,
#             tracker: Tracker,
#             domain: Dict[Text, Any]) -> List[Dict[Text, Any]]:
#
#         dispatcher.utter_message(text="Hello World!")
#
# #         return []
# import google.generativeai as genai
# from typing import Any, Text, Dict, List

# from rasa_sdk import Action, Tracker
# from rasa_sdk.executor import CollectingDispatcher

# # ⚠️ Gắn API Key trực tiếp (không khuyến nghị, nên để ENV)
# genai.configure(api_key="AIzaSyA71uCpu5SR3w2hYm5MhQ5XvKQGU8_YCsM")

# class ActionChatGemini(Action):
#     def name(self) -> Text: 
#         return "action_chat_gemini"

#     def run(
#         self,
#         dispatcher: CollectingDispatcher,
#         tracker: Tracker,
#         domain: Dict[Text, Any],
#     ) -> List[Dict[Text, Any]]:
#         """Gọi Gemini API để sinh câu trả lời"""

#         user_message = tracker.latest_message.get("text")
#         bot_reply = "Xin lỗi, tôi chưa có câu trả lời."

#         try:
#             model = genai.GenerativeModel("gemini-1.5-flash")
#             response = model.generate_content(user_message)

#             if hasattr(response, "text") and response.text:
#                 bot_reply = response.text

#         except Exception as e:
#             bot_reply = f"⚠️ Lỗi khi gọi Gemini: {str(e)}"

#         # Log để kiểm tra action có chạy đúng không
#         print(f"[Gemini] User: {user_message}")
#         print(f"[Gemini] Bot: {bot_reply}")

#         dispatcher.utter_message(text=bot_reply)

#         # 🚨 Quan trọng: phải return [] để action kết thúc
#         return []
import os
from dotenv import load_dotenv
import google.generativeai as genai
from typing import Any, Text, Dict, List
from rasa_sdk import Action, Tracker
from rasa_sdk.executor import CollectingDispatcher
from rasa_sdk.events import UserUtteranceReverted

# Load .env
load_dotenv()
genai.configure(api_key=os.getenv("GOOGLE_API_KEY"))

class ActionChatGemini(Action):
    def name(self) -> Text: 
        return "action_chat_gemini"

    def run(
        self,
        dispatcher: CollectingDispatcher,
        tracker: Tracker,
        domain: Dict[Text, Any],
    ) -> List[Dict[Text, Any]]:

        user_message = tracker.latest_message.get("text")
        bot_reply = "Xin lỗi, tôi chưa có câu trả lời."

        try:
            # model = genai.GenerativeModel("gemini-1.5-flash")
            model = genai.GenerativeModel("gemini-2.5-flash")
            response = model.generate_content(user_message)

            if hasattr(response, "text") and response.text:
                bot_reply = response.text
            elif hasattr(response, "candidates") and response.candidates:
                bot_reply = response.candidates[0].content

        except Exception as e:
            bot_reply = f"⚠️ Lỗi khi gọi Gemini: {str(e)}"

        print(f"[Gemini] User: {user_message}")
        print(f"[Gemini] Bot: {bot_reply}")

        dispatcher.utter_message(text=bot_reply)

        return [UserUtteranceReverted()]
