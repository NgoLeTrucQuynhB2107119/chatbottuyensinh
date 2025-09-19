import google.generativeai as genai

genai.configure(api_key="AIzaSyAebPn2m6lOTkjobzy73CmjtUA8W9kEM60")
model = genai.GenerativeModel("gemini-1.5-flash")

resp = model.generate_content("Xin chào, bạn là ai?")
print(resp.text)
