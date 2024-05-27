<div class="footer">
    <a href="{{ route('tos') }}" class="mx-4">Условия использования</a>
    <a href="{{ route('contacts') }}" class="mx-4">Контакты</a>
    <a href="{{ route('privacy') }}" class="mx-4">Политика конфиденциальности</a>
</div>


<style>
.footer {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding-bottom: 0.5rem;
  font-weight: bold;
  text-shadow: 2px 0 #111, -2px 0 #111, 0 2px #111, 0 -2px #111,
             1px 1px #111, -1px -1px #111, 1px -1px #111, -1px 1px #111;
}

@media (min-width: 768px) {
  .footer {
    flex-direction: row;
  }
}
</style>
