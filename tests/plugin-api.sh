#!/bin/bash

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Configuration
API_KEY=$1
BASE_URL="http://localhost:8080"
TEST_UUID="550e8400-e29b-41d4-a716-446655440000"

if [ -z "$API_KEY" ]; then
    echo -e "${RED}Please provide an API key:${NC} ./test-plugin-api.sh your-api-key"
    exit 1
fi

# Function to test endpoint
test_endpoint() {
    local endpoint=$1
    local method=${2:-GET}
    local data=$3

    echo -e "\n${YELLOW}Testing $method $endpoint${NC}"
    
    if [ "$method" = "GET" ]; then
        response=$(curl -s -X $method \
            -H "X-API-Key: $API_KEY" \
            -H "Content-Type: application/json" \
            "$BASE_URL$endpoint")
    else
        response=$(curl -s -X $method \
            -H "X-API-Key: $API_KEY" \
            -H "Content-Type: application/json" \
            -d "$data" \
            "$BASE_URL$endpoint")
    fi

    if [ $? -eq 0 ] && [ "$(echo $response | jq -r '.success')" = "true" ]; then
        echo -e "${GREEN}✓ Success${NC}"
        echo "Response: $response"
    else
        echo -e "${RED}✗ Failed${NC}"
        echo "Response: $response"
    fi
}

# Test all endpoints
echo -e "${YELLOW}Starting API tests...${NC}"

# Test balance endpoint
test_endpoint "/player/$TEST_UUID/balance"

# Test plots endpoint
test_endpoint "/player/$TEST_UUID/plots"

# Test companies endpoint
test_endpoint "/player/$TEST_UUID/companies"

# Test level endpoint
test_endpoint "/player/$TEST_UUID/level"

# Test inventory endpoint
test_endpoint "/player/$TEST_UUID/inventory"

# Test statistics endpoint
test_endpoint "/player/$TEST_UUID/statistics"

# Test error cases
echo -e "\n${YELLOW}Testing error cases...${NC}"

# Test invalid API key
response=$(curl -s -X GET \
    -H "X-API-Key: invalid-key" \
    -H "Content-Type: application/json" \
    "$BASE_URL/player/$TEST_UUID/balance")

if [ "$(echo $response | jq -r '.error.code')" = "invalid_api_key" ]; then
    echo -e "${GREEN}✓ Invalid API key test passed${NC}"
else
    echo -e "${RED}✗ Invalid API key test failed${NC}"
fi

# Test rate limiting
for i in {1..70}; do
    curl -s -X GET \
        -H "X-API-Key: $API_KEY" \
        -H "Content-Type: application/json" \
        "$BASE_URL/player/$TEST_UUID/balance" > /dev/null
done

response=$(curl -s -X GET \
    -H "X-API-Key: $API_KEY" \
    -H "Content-Type: application/json" \
    "$BASE_URL/player/$TEST_UUID/balance")

if [ "$(echo $response | jq -r '.error.code')" = "rate_limited" ]; then
    echo -e "${GREEN}✓ Rate limiting test passed${NC}"
else
    echo -e "${RED}✗ Rate limiting test failed${NC}"
fi

echo -e "\n${GREEN}Tests completed!${NC}" 